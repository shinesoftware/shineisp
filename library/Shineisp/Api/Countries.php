<?
class Shineisp_Api_Countries extends Shineisp_Api_Abstract_Action  {
    
    public function getAll() {
        $this->authenticate();
        
        return Countries::findAll();
    }
    
}