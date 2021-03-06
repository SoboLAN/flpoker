<?php

require_once 'vendor/autoload.php';

use FileListPoker\Main\Site;
use FileListPoker\Renderers\FullPageRenderer;
use FileListPoker\Renderers\FAQRenderer;
use FileListPoker\Content\FAQContent;

use Symfony\Component\HttpFoundation\Response;

$site = new Site();
$renderer = new FullPageRenderer($site);

$htmlout = $renderer->renderPage('faq.php');

$faqContent = new FAQContent();
$content = $faqContent->getFAQContent($site->getLanguage());

$faqMainTpl = file_get_contents('templates/faq/accordion.tpl');
$faqElementTpl = file_get_contents('templates/faq/accordion_element.tpl');
$faqRenderer = new FAQRenderer();
$renderedFAQ = $faqRenderer->render($content, $faqMainTpl, $faqElementTpl);

$bottomScript =
    '<script>
        $(function() {
            $("#faq-accordion").accordion({ active: false, heightStyle: "content", collapsible: true });
        });
    </script>';

$htmlout = str_replace(
    array('{content_type_id}', '{page_content}', '{bottom_page_scripts}'),
    array('content-narrower', $renderedFAQ, $bottomScript),
    $htmlout
);

$site->response->setContent($htmlout);
$site->response->setStatusCode(Response::HTTP_OK);
$site->response->send();