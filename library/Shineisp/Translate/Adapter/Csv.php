<?php
class Shineisp_Translate_Adapter_Csv extends Zend_Translate_Adapter_Csv
{
    public function _($messageId) {
        $translation = $this->translate($messageId, $this->getLocale());
        if(func_num_args() > 1) {
            $args = func_get_args();
            $args[0] = $translation;
            $translation = call_user_func_array('sprintf', $args);
        }
        return $translation;
    }
}