<?php
namespace Affiliate_Bridge\Sources
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class Source
    {
        protected $id               = null;
        protected $source           = null;
        protected $settings         = null;
        protected $priority         = null;

        protected $api              = null;

        protected $name             = null;
        protected $description      = null;
        protected $logo             = null;

        public function set( $key, $val )
        {
            $this->$key = $val;
            if ( $key === 'settings' ) {
                $this->settings = unserialize( $val );
            }
        }

        public function get_id()
        {
            return $this->id;
        }

        public function get_source()
        {
            return $this->source;
        }

        public function get_settings()
        {
            return $this->settings;
        }

        public function get_priority()
        {
            return $this->priority;
        }

        public  function get_name()
        {
            return $this->name;
        }

        public  function get_description()
        {
            return $this->description;
        }

        public  function get_logo()
        {
            return $this->logo;
        }

        public function is_active()
        {
            return ! is_null( $this->id );
        }

        public function get_column_source()
        {
            $return = '<div><div class="ab-source"><img src="' . $this->get_logo() . '" class="ab-source-logo">';
            $return .= '<span class="ab-helper"></span></div>';
            $return .= '<strong>' . $this->get_name() . '</strong></div>';

            return $return;
        }

        public function get_column_order()
        {
            if ( $this->is_active() ) {
                return '<div class="ab-priority"><input type="text" name="priority" value="' . $this->get_priority() . '" size="3"><span class="ab-helper"></span></div>';
            } else {
                return '<div class="ab-priority">This source is not currently active.<span class="ab-helper"></span></div>';
            }
        }

        public function get_column_actions()
        {
            $source = $this->get_source();
            $id     = $this->get_id();
            
            $new    = '<a href="' . admin_url('admin.php?page=affiliate-bridge-sources&action=new&source=' . $source ) . '" class="button-primary">Setup this Source</a>';
            $edit   = '<a href="' . admin_url('admin.php?page=affiliate-bridge-sources&action=edit&source-id=' . $id ) . '" class="button-secondary">Change Source Settings</a>';
            $cats   = '<a href="' . admin_url('admin.php?page=affiliate-bridge-sources&action=cats&source-id=' . $id ) . '" class="button-secondary">Download Category List</a>';
            $remove = '<a href="' . admin_url('admin.php?page=affiliate-bridge-sources&action=delete&source-id=' . $id ) . '" class="remove button-secondary button-remove">Remove this Source</a>';

            if ( $this->is_active() ) {
                $actions = '<div>' . $edit . ' ' . $cats . '<br><br>' . $remove . '</div>';
            } else {
                $actions = '<div>' . $new . /*' ' . $cats .*/ '</div>';
            }

            return '<div class="ab-actions">' . $actions . '<span class="ab-helper"></span></div>';
        }
    }
}