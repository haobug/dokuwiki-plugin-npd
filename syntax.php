<?php
/**
 * Keyboard Syntax Plugin: Marks text as keyboard key presses.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Gina Haeussge <osd@foosel.net>
 * @author     Christopher Arndt
 */

if(!defined('DOKU_INC'))
  define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_npd extends DokuWiki_Syntax_Plugin {

    /**
     * return some info
     */
    function getInfo() {
        return confToHash(dirname(__FILE__).'/plugin.info.txt');
    }

    function getType() { return 'formatting'; }

    function getSort(){ return 135; }

    function connectTo($mode) {
         $this->Lexer->addEntryPattern('~~NEWPAGE', $mode, 'plugin_npd');
    }

    function postConnect() {
        $this->Lexer->addExitPattern('~~', 'plugin_npd');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler) {
        switch ($state) {
            case DOKU_LEXER_ENTER :
            case DOKU_LEXER_UNMATCHED :
            case DOKU_LEXER_EXIT:
                return array($state, '');
        }
        return array();
    }

    /**
     * Create output
     */
    function render($mode, &$renderer, $data) {
        if ($mode == 'xhtml') {
            list($state, $matches) = $data;
			switch ($state) {
				case DOKU_LEXER_ENTER :
					if(!($helper = plugin_load('helper', 'npd'))) return false;
					$renderer->doc .= $helper->html_new_page_button(true);
					return true;
				case DOKU_LEXER_UNMATCHED :
				case DOKU_LEXER_EXIT:
					return true;
			}
        }
        return false;
    }
}
