<script>
    window.donationTranslations = {{ Illuminate\Support\Js::from(json_decode(file_get_contents(lang_path(app()->getLocale().'.json')), true)) }};
</script>
