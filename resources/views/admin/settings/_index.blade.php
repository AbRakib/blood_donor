<section id="settingsView">
    <form id="settingsForm" class="settings-layout" action="{{ route('admin.settings.upload') }}" method="POST" enctype="multipart/form-data">
        @include('admin.settings.modal._edit')
    </form>
</section>
