<?php
/**
 * @package Dforms
 */

namespace Develtio\WP\Forms\Core\Api;

class SettingsApi
{
    public $admin_pages = [];

    private $admin_subpages = [];

    public $settings = [];

    public $sections = [];

    public $fields = [];

    public function init()
    {
        if ( !empty( $this->admin_pages ) || !empty( $this->admin_subpages ) ) {
            add_action( 'admin_menu', [ $this, 'addAdminMenu' ] );
        }

        if ( !empty( $this->settings ) ) {
            add_action( 'admin_init', [ $this, 'registerCustomFields' ] );
        }
    }

    public function addPages( array $pages )
    {
        $this->admin_pages = $pages;

        return $this;
    }

    public function withSubPage( string $title = null )
    {
        if ( empty( $this->admin_pages ) ) {
            return $this;
        }

        $admin_page = $this->admin_pages[0];

        $this->admin_subpages = [
            [
                'parent_slug' => $admin_page['menu_slug'],
                'page_title' => $admin_page['page_title'],
                'menu_title' => $title ? $title : $admin_page['menu_title'],
                'capability' => $admin_page['capability'],
                'menu_slug' => $admin_page['menu_slug'],
                'callback' => $admin_page['callback']
            ]
        ];;

        return $this;
    }

    public function addSubPages( array $pages )
    {
        $this->admin_subpages = array_merge( $this->admin_subpages, $pages );

        return $this;
    }

    public function addAdminMenu()
    {
        foreach ( $this->admin_pages as $page ) {
            add_menu_page(
                $page['page_title'],
                $page['menu_title'],
                $page['capability'],
                $page['menu_slug'],
                $page['callback'],
                $page['icon_url'],
                $page['position']
            );
        }

        foreach ( $this->admin_subpages as $page ) {
            add_submenu_page(
                $page['parent_slug'],
                $page['page_title'],
                $page['menu_title'],
                $page['capability'],
                $page['menu_slug'],
                $page['callback']
            );
        }
    }

    public function setSettings( array $settings )
    {
        $this->settings = $settings;

        return $this;
    }

    public function setSections( array $sections )
    {
        $this->sections = $sections;

        return $this;
    }

    public function setFields( array $fields )
    {
        $this->fields = $fields;

        return $this;
    }

    public function registerCustomFields()
    {
        foreach ( $this->settings as $setting ) {
            register_setting(
                $setting["option_group"],
                $setting["option_name"],
                isset( $setting["callback"] ) ? $setting["callback"] : ''
            );
        }

        foreach ( $this->sections as $section ) {
            add_settings_section(
                $section["id"],
                $section["title"],
                isset( $section["callback"] ) ? $section['callback'] : '',
                $section["page"]
            );
        }

        foreach ( $this->fields as $field ) {
            add_settings_field(
                $field['id'],
                $field['title'],
                isset( $field['callback'] ) ? $field['callback'] : '',
                $field['page'],
                $field['section'],
                isset( $field['args'] ) ? $field['args'] : ''
            );
        }
    }
}
