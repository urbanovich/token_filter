<?php

class FrontController extends FrontControllerCore {

    /**
     * Renders controller templates and generates page content
     *
     * @param array|string $content Template file(s) to be rendered
     * @throws Exception
     * @throws SmartyException
     */
    protected function smartyOutputContent($content)
    {
        $this->context->cookie->write();

        $html = '';
        $js_tag = 'js_def';
        $this->context->smarty->assign($js_tag, $js_tag);

        if (is_array($content)) {
            foreach ($content as $tpl) {
                $html .= $this->context->smarty->fetch($tpl);
            }
        } else {
            $html = $this->context->smarty->fetch($content);
        }

        $html = trim($html);

        //set hook replaceToken
        if($results = Hook::exec('replaceToken', array('tokens' => TokenFilter::getAllTokens()), null, true))
        {
            $search_tokens = array();
            $replace_tokens = array();
            foreach($results as $result)
            {
                foreach($result['tokens'] as $token)
                {
                    $search_tokens[] = '[' . $token['name'] . ']';
                    $replace_tokens[] = $token['content'];
                }
            }

            $html = str_replace($search_tokens, $replace_tokens, $html);
        }

        if (in_array($this->controller_type, array('front', 'modulefront')) && !empty($html) && $this->getLayout()) {
            $live_edit_content = '';
            if (!$this->useMobileTheme() && $this->checkLiveEditAccess()) {
                $live_edit_content = $this->getLiveEditFooter();
            }

            $dom_available = extension_loaded('dom') ? true : false;
            $defer = (bool)Configuration::get('PS_JS_DEFER');

            if ($defer && $dom_available) {
                $html = Media::deferInlineScripts($html);
            }
            $html = trim(str_replace(array('</body>', '</html>'), '', $html))."\n";

            $this->context->smarty->assign(array(
                $js_tag => Media::getJsDef(),
                'js_files' =>  $defer ? array_unique($this->js_files) : array(),
                'js_inline' => ($defer && $dom_available) ? Media::getInlineScript() : array()
            ));

            $javascript = $this->context->smarty->fetch(_PS_ALL_THEMES_DIR_.'javascript.tpl');

            if ($defer && (!isset($this->ajax) || ! $this->ajax)) {
                echo $html.$javascript;
            } else {
                echo preg_replace('/(?<!\$)'.$js_tag.'/', $javascript, $html);
            }
            echo $live_edit_content.((!isset($this->ajax) || ! $this->ajax) ? '</body></html>' : '');
        } else {
            echo $html;
        }
    }
}