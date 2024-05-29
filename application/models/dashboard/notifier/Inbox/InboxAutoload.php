<?php

namespace notifier\Inbox;

use Developer\Tools\Url as Url;
use Dashboard\breadcrumb as breadcrumb;

/**
 * auto load class
 */
class InboxAutoload extends InboxConfig {

    /**
     * Breadcrumbs
     * @var array 
     */
    var $breadcrumbs = array();

    /**
     * what action should load the objects ?
     * @return Void
     */
    private function swich_call() {
        $param = Url::getURL($this->URL_ACTION);
        switch ($param) {
            case 'preview':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new InboxPreview; // load module application for mode preview
                break;
            case 'add':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new InboxInsert; // load module application for mode add
                break;
            case 'alt':
                new breadcrumb(FILENAME, $this->breadcrumbs[1]);
                new InboxUpdate; // load module application for mode add
                break;
            case 'del':
                new InboxActions;
                break;
            case 'delete_all':
                new InboxActions;
                break;
            case 'spam_all':
                new InboxActions;
                break;
            case 'important_all':
                new InboxActions;
                break;
            case 'delete_all_permanent':
                new InboxActions;
                break;
            case 'reply':
                new InboxActions;
                break;
            case 'search':
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new InboxSearch;
                break;
            case 'mark_all' :
                new InboxActions();
                break;
            case 'mark_all_star':
                new InboxActions();
                break;
            default :
                new breadcrumb(FILENAME, $this->breadcrumbs[0]);
                new InboxListing; // load module application for default (listing)
                break;
        }
    }

    /**
     * Check
     * @return Void
     */
    public function __construct($breadcrumbs) {
        $this->breadcrumbs = $breadcrumbs;
        $this->swich_call();
    }

}
