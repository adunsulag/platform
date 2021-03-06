<?php

namespace Oro\Bundle\ConfigBundle\Tests\Unit\Config\Tree;

use Oro\Bundle\ConfigBundle\Config\Tree\AbstractNodeDefinition;

class AbstractNodeDefinitionTest extends \PHPUnit\Framework\TestCase
{
    const TEST_NAME     = 'testNodeName';
    const TEST_PRIORITY = 255;

    /** @var AbstractNodeDefinition */
    protected $node;

    protected function setUp()
    {
        $this->node = $this->getMockForAbstractClass(
            'Oro\Bundle\ConfigBundle\Config\Tree\AbstractNodeDefinition',
            array(self::TEST_NAME, array())
        );
    }

    protected function tearDown()
    {
        unset($this->node);
    }

    public function testGetName()
    {
        $this->assertEquals(self::TEST_NAME, $this->node->getName());
    }

    public function testPrepareDefinition()
    {
        // should set default definition values
        $this->assertEquals(0, $this->node->getPriority());
    }

    public function testSetGetPriority()
    {
        $this->node->setPriority(self::TEST_PRIORITY);
        $this->assertEquals(self::TEST_PRIORITY, $this->node->getPriority());
    }
}
