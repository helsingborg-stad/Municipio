<div id="translate" class="google-translate {{ get_field('show_google_translate', 'option') != 'fold' ? 'creamy creamy-border-bottom gutter gutter-padding gutter-vertical' : 'google-translate-fold' }} target-toggle">
    <div class="container">
        <div class="grid">
            <div class="grid-sm-12">
                <h3>Translate</h3>
                <p>
                    You can use Google Translate to translate the contents of {{ explode('://', get_home_url())[1] }}. To do that, select the language you would like to translate into in the lost below.
                    <br><small>Please bear in mind, since the Google Translate is an automatically generated translation, we do not take any responsibility for errors in the text.</small>
                </p>
                <div id="google-translate-element" class="gutter gutter-vertical"></div>
                <a href="#" class="btn btn-primary">Close</a>
            </div>
        </div>
    </div>
</div>
