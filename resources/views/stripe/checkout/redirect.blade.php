<!DOCTYPE html>
<html>
<head>
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <script type="text/javascript">
        Stripe("{{ $stripe_publishable_key }}").redirectToCheckout({ sessionId: "{{ $stripe_session_id }}" });
    </script>
</body>
</html>
