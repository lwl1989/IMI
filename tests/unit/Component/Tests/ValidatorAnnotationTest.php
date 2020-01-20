<?php
namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Bean\BeanFactory;
use PHPUnit\Framework\Assert;
use Imi\Validate\ValidatorHelper;
use Imi\Test\Component\Enum\TestEnum;
use Imi\Test\Component\Validate\Classes\TestValidator;
use Imi\Test\Component\Validate\Classes\TestAutoConstructValidator;

/**
 * @testdox Validator Annotation
 */
class ValidatorAnnotationTest extends BaseTest
{
    /**
     * @var \Imi\Test\Component\Validate\Classes\TestValidator
     */
    private $tester;

    private $data;

    public function testValidatorAnnotation()
    {
        $this->tester = new TestValidator($this->data);
        $this->success();
        $this->compareFail();
        $this->decimalFail();
        $this->enumFail();
        $this->inFail();
        $this->intFail();
        $this->requiredFail();
        $this->numberFail();
        $this->textFail();
        $this->validateValueFail();
        $this->optional();
    }

    public function testAutoConstructValidator()
    {
        $this->initData();
        $test = BeanFactory::newInstance(TestAutoConstructValidator::class, $this->data);

        // int fail
        $this->data['int'] = 1000;
        try {
            $test = BeanFactory::newInstance(TestAutoConstructValidator::class, $this->data);
            $this->assertTrue(false, 'Construct validate property fail');
        } catch(\Throwable $th) {
            $this->assertStringEndsWith('1000 不符合大于等于0且小于等于100', $th->getMessage());
        }

        try {
            $test = new TestAutoConstructValidator;
            $this->assertTrue(false, 'Construct validate fail');
        } catch(\Throwable $th) {
        }
    }

    public function testMethodAutoValidate()
    {
        $this->initData();
        $test = BeanFactory::newInstance(TestAutoConstructValidator::class, $this->data);
        $this->assertEquals(1, $test->test(1));
        try {
            $test->test(-1);
            $this->assertTrue(false, 'Method validate fail');
        } catch(\Throwable $th) {
        }
    }

    private function initData()
    {
        $this->data = [
            'compare'       =>  -1,
            'decimal'       =>  1.25,
            'enum'          =>  TestEnum::A,
            'in'            =>  1,
            'int'           =>  1,
            'required'      =>  '',
            'number'        =>  1,
            'text'          =>  'imiphp.com',
            'validateValue' =>  -1,
            'optional'      =>  1,
        ];
    }

    private function success()
    {
        $this->initData();
        $result = $this->tester->validate();
        $this->assertTrue($result, $this->tester->getMessage() ?: '');
    }
    
    private function compareFail()
    {
        $this->initData();
        $this->data['compare'] = 1;
        $this->assertFalse($this->tester->validate());
    }

    private function decimalFail()
    {
        $this->initData();
        $this->data['decimal'] = 1.222;
        $this->assertFalse($this->tester->validate());

        $this->data['decimal'] = 0;
        $this->assertFalse($this->tester->validate());

        $this->data['decimal'] = 11;
        $this->assertFalse($this->tester->validate());
    }

    private function enumFail()
    {
        $this->initData();
        $this->data['enum'] = 100;
        $this->assertFalse($this->tester->validate());
    }

    private function inFail()
    {
        $this->initData();
        $this->data['in'] = 100;
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('100 不在列表内', $this->tester->getMessage());
    }

    private function intFail()
    {
        $this->initData();
        $this->data['int'] = -1;
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('-1 不符合大于等于0且小于等于100', $this->tester->getMessage());
    }

    private function requiredFail()
    {
        $this->initData();
        unset($this->data['required']);
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('required为必须参数', $this->tester->getMessage());
    }

    private function numberFail()
    {
        $this->initData();
        $this->data['number'] = 1.234;
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('数值必须大于等于0.01，小于等于999.99，小数点最多保留2位小数，当前值为1.234', $this->tester->getMessage());

        $this->data['number'] = 0;
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('数值必须大于等于0.01，小于等于999.99，小数点最多保留2位小数，当前值为0', $this->tester->getMessage());
    }

    private function textFail()
    {
        $this->initData();
        $this->data['text'] = '';
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('text参数长度必须>=6 && <=12', $this->tester->getMessage());

        $this->data['text'] = '1234567890123';
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('text参数长度必须>=6 && <=12', $this->tester->getMessage());
    }

    private function validateValueFail()
    {
        $this->initData();
        $this->data['validateValue'] = '1';
        $this->assertFalse($this->tester->validate());
    }

    private function optional()
    {
        $this->initData();
        $this->data['optional'] = -1;
        $this->assertFalse($this->tester->validate());

        unset($this->data['optional']);
        $this->assertTrue($this->tester->validate());
    }

}
