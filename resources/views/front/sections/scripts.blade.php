<script src="{{ asset('front/js/inos.js') }}"></script>

<script>
    "use strict";

    function callToAction() {
        var registerUrl = "{{ route('front.register') }}";

        art.sendXhr({
            url: "{{ route('front.call-to-action') }}",
            type: "POST",
            file: true,
            container: "#callToAction",
            disableButton: true,
            messageLocation: 'none',
            success: function(response) {
                if (response.status == 'success') {
                    var actionEmail = document.getElementById('action_email').value;
                    window.location.href = registerUrl + '?email=' + encodeURIComponent(actionEmail);
                }
            }
        });
    }

    function changeLang(langKey) {
        art.sendXhr({
            url: "{{ route('front.change-language') }}",
            type: "POST",
            data: { key: langKey },
            container: "#ajax-register-form",
            success: function(response) {
                if (response.status == 'success') {
                    window.location.reload();
                }
            }
        });
    }
</script>
