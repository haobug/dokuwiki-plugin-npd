<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Pierre Spring <pierre.spring@liip.ch>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
class helper_plugin_npd extends DokuWiki_Plugin 
{

    function getInfo()
    {
        return confToHash(dirname(__FILE__).'/plugin.info.txt');
    }

    function getMethods()
    {
        $result = array();
        $result[] = array(
            'name'   => 'html_new_page_button',
            'desc'   => 'include a html button',
            'params' => array(),
            'return' => array()
        );
        return $result;
    }

    function html_new_page_button($return = false)
    {
        global $conf;
        global $ID;

        /* don't show the button if the user doesn't have edit permissions */
        if(auth_quickaclcheck($ID) < AUTH_EDIT) {
            $non_login = $this->getConf('non_login_show');
            if(!$non_login)
                return '';
        }

        $label = $this->getLang('btn_create_new_page');
        if (!$label) {
            // needs translation ;)
            $label = 'Create New Page';
        }

        $tip = htmlspecialchars($label);

        $ret = '';

        //filter id (without urlencoding)
        $id = idfilter($ID,false);


        if(!isset($_SERVER['REMOTE_USER'])) {
            $act = tpl_get_action('login');
            if(strpos($id, '#') === 0) {
                $url = $id;
            } else {
                $url = wl($id, $act['params']);
            }

            $link_type = $this->getConf('link_type');
            switch ($link_type) {
            case 'link':
                $out = tpl_link($url, $label, '', true);
                break;
            default:
                $out = html_btn('create_new', $id, false, $params, 'get','', $label);
                break;
            }
            if ($return) {
                return $out;
            } else {
                echo $out;
				return '';
            }
        } else {
        //make nice URLs even for buttons
        if($conf['userewrite'] == 2){
            $script = DOKU_BASE.DOKU_SCRIPT.'/'.$id . "?";
        }elseif($conf['userewrite']){
            $script = DOKU_BASE.$id."?";
        }else{
            $script = DOKU_BASE.DOKU_SCRIPT . "?";
            $params['id'] = $id;
        }
        $params['idx'] = ":" . getNS($ID);
        $params['npd'] = 1;

        $url = $script;

        if(is_array($params)){
            reset($params);
            while (list($key, $val) = each($params)) {
                $url .= $key.'=';
                $url .= htmlspecialchars($val.'&');
            }
        }
        }
        $link_type = $this->getConf('link_type');

        switch ($link_type) {
        case 'link':
            $ret .= '<a rel="nofollow" href="'.$url.'" style="display:none;" id="npd_create_button" class="action npd">'.$label.'</a>';
            break;
        default:
            $ret .= '<form class="button" action="'.$url.'"><div class="no">';
            $ret .= '<input id="npd_create_button" type="submit" value="'.htmlspecialchars($label).'" class="button" ';
            $ret .= 'title="'.$tip.'" ';
            // the button will be enabled by js, as it does not
            // make any sense in a browser without js ;)
            $ret .= 'style="display: none;" ';
            $ret .= '/>';
            $ret .= '</div>';
            $ret .= '</form>';
        }

        if ($return) {
            return $ret;
        } else {
            echo $ret;
        }
    }
}

