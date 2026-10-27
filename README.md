# মান্দা রক্ত দান কর্মসূচি — Laravel Database & Backend Plan

## Installation
1. Copy `database/migrations/*.php` to your Laravel project's `database/migrations/` directory.
2. IMPORTANT: These are fresh-install migrations. If earlier `blood_groups` or `blood_donors` migrations have already run, **do not create duplicate tables**. Either replace those migrations before their first run, or write ALTER migrations for deployed databases. Never run `migrate:fresh` against production data.
3. Run `php artisan migrate`.
4. Seed eight blood groups (A+, A-, B+, B-, AB+, AB-, O+, O-), then Bangladesh districts/upazilas/unions from a verified source.
5. Use Laravel's stock `users` table (or your existing admin-auth table). The fixed `created_by` fields are `unsignedInteger` as requested; avoid foreign keys to `users.id` where it is `unsignedBigInteger`.

## Tables and relationships
- `blood_groups` → hasMany donors and blood_requests
- `districts` → hasMany upazilas, donors, requests
- `upazilas` → belongsTo district; hasMany unions, donors, requests
- `unions` → belongsTo upazila; hasMany donors
- `blood_donors` → belongsTo blood_group/district/upazila/union; hasMany donations, request_donors, contact_requests, availability_logs
- `blood_requests` → belongsTo blood_group/district/upazila; hasMany request_donors, donations, request_status_logs
- `request_donors` → donor/request matching (unique composite)
- `blood_donations` → verified donation transactions, optionally attached to request
- `blood_request_status_logs` → status history
- `donor_availability_logs` → availability history
- `donor_contact_requests` → protect donor phone number by routing contact via admin
- `activity_logs` → audit trail
- `system_settings` → admin-managed configuration

## Directory / class structure
```text
app/
  Models/
    BloodGroup.php District.php Upazila.php Union.php
    BloodDonor.php BloodRequest.php RequestDonor.php BloodDonation.php
    BloodRequestStatusLog.php DonorAvailabilityLog.php
    DonorContactRequest.php ActivityLog.php SystemSetting.php
  Http/
    Controllers/Admin/
      DashboardController.php BloodDonorController.php
      BloodRequestController.php BloodGroupController.php
      LocationController.php DonationController.php
      ContactRequestController.php ReportController.php SettingController.php
    Controllers/Public/
      DonorSearchController.php DonorRegistrationController.php
      BloodRequestSubmissionController.php
    Requests/
      StoreBloodDonorRequest.php UpdateBloodDonorRequest.php
      StoreBloodRequestRequest.php UpdateBloodRequestRequest.php
      StoreDonationRequest.php
    Resources/
      BloodDonorResource.php BloodRequestResource.php
  Services/
    DonorSearchService.php DonorService.php BloodRequestService.php
    DonorMatchingService.php DonationService.php DashboardService.php
    NotificationService.php ReportService.php
  Policies/
    BloodDonorPolicy.php BloodRequestPolicy.php
  Jobs/
    NotifyPotentialDonorsJob.php ExpireOverdueRequestsJob.php
  Observers/
    BloodDonorObserver.php BloodRequestObserver.php
  Support/
    AuditStamp.php
routes/
  web.php api.php
```

## Core functions / use cases
### DashboardService
- `getSummary(): array`: total active donors, currently available donors, pending/urgent requests, groups count
- `getRecentRequests(int $limit = 5)`: urgent request list
- `getGroupBreakdown(): array`: group-wise donor counts
- `getMonthlyDonationTrend()`: verified donations by month

### DonorService
- `create(array $data)`: validate, generate unique donor_code, record consent, stamp created_by
- `update(BloodDonor $donor, array $data)`: update fields + audit
- `setAvailability(BloodDonor $donor, bool $available, ?string $reason)`: availability history
- `softDelete(BloodDonor $donor)`: set deleted=1, deleted_at, deleted_by (no hard-delete)
- `getDonationHistory(BloodDonor $donor)`: verified donation history

### DonorSearchService
- `search(array $filters)`: blood_group_id, district_id, upazila_id, union_id, is_available, name, paginated
- Always filter `status = 1` and `deleted = 0`; for public results, never include private phone unless explicitly consented to disclosure.

### BloodRequestService
- `create(array $data)`; `verify(BloodRequest $request)`
- `changeStatus(BloodRequest $request, string $toStatus, ?string $reason)`: append status log
- `assignDonor(BloodRequest $request, BloodDonor $donor)`; `close()`; `cancel()`
- `expireOverdue()`: scheduled expiry after needed_at where appropriate

### DonorMatchingService
- `suggest(BloodRequest $request)`: match verified blood group and location, active/available/consenting donors, exclude unavailable/recent donors, avoid previous decline/unreachable.
- Matching is a **contact shortlist**, not a medical determination of transfusion compatibility or donation fitness; trained clinicians/blood bank confirm eligibility and compatibility.

### DonationService
- `record(array $data)`; `verify(BloodDonation $donation)`; `reject()`
- In DB transaction, verified donations update donor.last_donation_date, total_donations and optionally request.units_fulfilled and request status. Count only verified donations. Guard double verification.

### ContactRequestController
- `store()`: requester asks admin to contact donor; protect donor mobile
- `approve()/reject()`: admin mediation

### ReportService
- `donorSummary()`; `requestSummary()`; `donationSummary()`; `exportDonorsCsv()` (authorized admin only)

## Suggested HTTP endpoints
| Method | Endpoint | Purpose |
|---|---|---|
| GET | /admin/dashboard | Statistics |
| GET/POST | /admin/donors | List/create donors |
| GET/PUT/DELETE | /admin/donors/{donor} | Show/update/custom soft delete |
| PATCH | /admin/donors/{donor}/availability | Toggle availability |
| GET/POST | /admin/blood-requests | List/create requests |
| GET/PATCH | /admin/blood-requests/{request} | Detail/update |
| PATCH | /admin/blood-requests/{request}/status | Status transition |
| POST | /admin/blood-requests/{request}/match | Generate shortlist |
| POST | /admin/blood-requests/{request}/assign-donor | Assign donor |
| GET/POST | /admin/blood-groups | List/create groups |
| GET | /admin/locations/upazilas?district_id=... | Dependent dropdown |
| GET | /admin/locations/unions?upazila_id=... | Dependent dropdown |
| GET/POST | /admin/donations | List/record donations |
| POST | /admin/donations/{donation}/verify | Verify and recalculate counts |
| GET | /admin/reports | Reports |
| GET/PUT | /admin/settings | Settings |
| GET | /donors | Public privacy-safe donor search |
| POST | /donor-registration | Public donor signup (verified consent) |
| POST | /blood-requests | Public blood request submission |
| POST | /donor-contact-requests | Contact via admin |

## Laravel implementation essentials
- Use `auth` and `can`/policies for admin routes; CSRF for web writes; throttle public endpoints; validate Bangladeshi numbers server-side.
- Every model with fixed timestamps needs either `public $timestamps = false` and explicitly set timestamps in a shared observer/trait, or native timestamp support configured carefully. No default `softDeletes()` because you use `deleted` integer.
- Restrict updates to safe fields using `$fillable`; never accept `created_by`, `deleted_by`, `verified_by`, or `status` unguarded from public requests.
- Prevent revealing donor phone numbers through HTML, APIs, CSV, or browser-side JSON without access checks and informed consent.
- Restrict personal data exports, log access, and delete/retain data according to policy.
- Validate village/union/upazila/district hierarchy server-side.
- Use database transactions + row locking for verifying donations and filling units to prevent double counting; statuses and counts should never rely only on client state.
- Dashboard urgent/pending count should come from database rows, not the frontend placeholder cards.
- Schedule request expiry and notification work via Laravel Scheduler/Queue.
- Follow healthcare provider/blood bank medical screening guidance; do not equate a last donation date with medical eligibility.
