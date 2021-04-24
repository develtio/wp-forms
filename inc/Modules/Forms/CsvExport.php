<?php
/**
 * @package Dforms
 */

namespace Develtio\WP\Forms\Modules\Forms;

class CsvExport
{
    private $cpt_name;
    private $fields;

    public function __construct($cpt_name, $fields)
    {
        $this->cpt_name = "df-".sanitize_title(strtolower($cpt_name));
        $this->fields = $fields;

        add_action('views_edit-'.$this->cpt_name, [$this,'addExportButton'], 10, 4);
        add_action('admin_init', [$this,'export'], 10);
    }

    public function addExportButton($views)
    {
        $views['export'] = '<a href="?post_type='.$this->cpt_name.'&export_cpt=true" class="primary">CSV Export</a><style>.page-title-action{display:none !important; }</style>';
        return $views;
    }

    public function export()
    {
        if (!isset($_GET['export_cpt'])) {
            return false;
        }

        if(!is_user_logged_in()){
            die();
        }

        if (!current_user_can('administrator')) {
            die();
        }

        $objects = get_posts([
            'post_type' => $this->cpt_name,
            'posts_per_page' => -1
        ]);

        $csv = [];
        $header = [];
        foreach ($this->fields as $fieldKey => $fieldName) {
            $header[] = $fieldName;
        }
        $csv[] = $header;

        foreach ($objects as $object) {
            $line = [];
            $values = get_post_meta($object->ID);

            foreach($this->fields as $fieldKey => $fieldName){

                // post ID
                if($fieldKey === 'id'){
                    $line[] = $object->ID;
                    continue;
                }

                // post crated at
                if($fieldKey === 'date'){
                    $line[] = $object->post_date;
                    continue;
                }

                // serialized field - file?
                if(isset($values[$fieldKey]) && isset($values[$fieldKey][0]) && is_serialized($values[$fieldKey][0])){
                    $unserialized = unserialize($values[$fieldKey][0]);
                    if(isset($unserialized['url'])) {
                        $line[] = $unserialized['url'];
                        continue;
                    }
                }

                // common field
                if(isset($values[$fieldKey]) && isset($values[$fieldKey][0])){
                    $line[] = $values[$fieldKey][0];
                }
            }

            $csv[] = $line;
        }

        $f = fopen('php://temp/maxmemory:' . (50 * 1024 * 1024), 'r+');
        foreach ($csv as $line) {
            fputcsv($f, $line, ';');
        }
        rewind($f);

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$this->cpt_name.'-'. date('Y-m-d-H:i:s') . '.csv";');

        echo stream_get_contents($f);
        die();
    }

}