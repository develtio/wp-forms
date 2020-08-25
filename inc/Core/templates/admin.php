<div class="wrap">
    <h1>Dashboard</h1>
    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
        settings_fields( 'develtio_plugin_settings' );
        do_settings_sections( 'develtio_plugin' );
        submit_button();
        ?>
    </form>
</div>


