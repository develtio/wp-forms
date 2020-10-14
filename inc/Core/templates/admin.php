<div class="wrap">
    <h1><?php echo __('Dashboard', 'develtio-forms') ?></h1>
    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
        settings_fields( 'develtio_plugin_settings' );
        do_settings_sections( 'develtio_plugin' );
        submit_button(__('Save', 'develtio-forms'));
        ?>
    </form>
</div>


