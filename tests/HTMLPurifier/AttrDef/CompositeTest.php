<?php

require_once 'HTMLPurifier/AttrDef/Composite.php';

class HTMLPurifier_AttrDef_Composite_Testable extends
      HTMLPurifier_AttrDef_Composite
{
    
    function HTMLPurifier_AttrDef_Composite_Testable(&$defs) {
        $this->defs =& $defs;
    }
    
}

class HTMLPurifier_AttrDef_CompositeTest extends HTMLPurifier_AttrDefHarness
{
    
    var $def1, $def2;
    
    function test() {
        
        generate_mock_once('HTMLPurifier_AttrDef');
        
        $config = HTMLPurifier_Config::createDefault();
        $context = new HTMLPurifier_AttrContext();
        
        // first test: value properly validates on first definition
        // so second def is never called
        
        $def1 =& new HTMLPurifier_AttrDefMock($this);
        $def2 =& new HTMLPurifier_AttrDefMock($this);
        $defs = array(&$def1, &$def2);
        $def =& new HTMLPurifier_AttrDef_Composite_Testable($defs);
        $input = 'FOOBAR';
        $output = 'foobar';
        $def1_params = array($input, $config, $context);
        $def1->expectOnce('validate', $def1_params);
        $def1->setReturnValue('validate', $output, $def1_params);
        $def2->expectNever('validate');
        
        $this->assertIdentical($output,
            $def->validate($input, $config, $context));
        
        $def1->tally();
        $def2->tally();
        
        // second test, first def fails, second def works
        
        $def1 =& new HTMLPurifier_AttrDefMock($this);
        $def2 =& new HTMLPurifier_AttrDefMock($this);
        $defs = array(&$def1, &$def2);
        $def =& new HTMLPurifier_AttrDef_Composite_Testable($defs);
        $input = 'BOOMA';
        $output = 'booma';
        $def_params = array($input, $config, $context);
        $def1->expectOnce('validate', $def_params);
        $def1->setReturnValue('validate', false, $def_params);
        $def2->expectOnce('validate', $def_params);
        $def2->setReturnValue('validate', $output, $def_params);
        
        $this->assertIdentical($output,
            $def->validate($input, $config, $context));
        
        $def1->tally();
        $def2->tally();
        
        // third test, all fail, so composite faiils
        
        $def1 =& new HTMLPurifier_AttrDefMock($this);
        $def2 =& new HTMLPurifier_AttrDefMock($this);
        $defs = array(&$def1, &$def2);
        $def =& new HTMLPurifier_AttrDef_Composite_Testable($defs);
        $input = 'BOOMA';
        $output = false;
        $def_params = array($input, $config, $context);
        $def1->expectOnce('validate', $def_params);
        $def1->setReturnValue('validate', false, $def_params);
        $def2->expectOnce('validate', $def_params);
        $def2->setReturnValue('validate', false, $def_params);
        
        $this->assertIdentical($output,
            $def->validate($input, $config, $context));
        
        $def1->tally();
        $def2->tally();
        
    }
    
}

?>