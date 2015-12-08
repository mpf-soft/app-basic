<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 8/11/14
 * Time: 11:43 AM
 */

namespace app\components;


//use app\components\htmltools\Page;
use mpf\widgets\form\fields\Markdown;

class Controller extends \mpf\web\Controller {

    protected function init($config = array()) {
        //Page::get()->getTranslator()->setLanguage('ro');
        return parent::init($config);
    }

    protected function beforeAction($actionName) {
        if (isset($_POST['MarkdownPreview'])){ // only needed when using markdown
            echo Markdown::processText(htmlentities($_POST['text']));
            return false;
        }

        return parent::beforeAction($actionName);
    }

    protected function afterAction($actionName, &$result) {

        return parent::afterAction($actionName, $result);
    }

} 