<?
class Shineisp_Api_Shineisp_Countries extends Shineisp_Api_Shineisp_Abstract_Action  {
    
    public function getAll() {
        $this->authenticate();
        
        return Countries::getList();
    }
    
}