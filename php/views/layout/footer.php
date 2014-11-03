</div>
<div id="footer">
    Powered by MPF Framework
</div>
</div>
</div>
<div id="fb-root"></div>
</body>
</html>
<script>(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=<?= \app\models\GlobalConfig::value('FACEBOOK_APPID'); ?>&version=v2.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
