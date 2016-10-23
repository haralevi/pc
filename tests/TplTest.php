<?php
namespace photocommunity\mobile;

require_once dirname(__FILE__) . '/../classes/Tpl.php';

class TplTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Tpl
     */
    public $tpl;
    public $content;
    public $content_parsed;
    public $tpl_var;

    protected function setUp()
    {
        # init necessary objects
        Config::$documentRoot = '../';
        $this->tpl = new Tpl();
        $this->content = '<div class="wrapContentImg clearfix" id="ajaxBody">{work}<div class="wrapContent">{comments}</div></div>';
        $this->tpl_var['work'] = 'work';
        $this->tpl_var['comments'] = 'comments';
        $this->content_parsed = '<div class="wrapContentImg clearfix" id="ajaxBody">work<div class="wrapContent">comments</div></div>';
    }

    public function prepareResult($result) {
        $result = trim($result);
        $result = str_replace(Consta::EOL, '', $result);
        $result = preg_replace('/[ \t]{2,}/', '', $result);
        return $result;
    }

    public function testOpen()
    {
        $this->tpl->open('work');
        $result = $this->prepareResult($this->tpl->content);
        $this->assertEquals($this->content, $result);
    }

    public function testParse()
    {
        $this->tpl->open('work');
        $this->tpl->parse($this->tpl_var);
        $result = $this->prepareResult($this->tpl->content);
        $this->assertEquals($this->content_parsed, $result);
    }

    protected function tearDown()
    {
        # delete unnecessary objects
    }
}
