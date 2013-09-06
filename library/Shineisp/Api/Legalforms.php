<?
class Shineisp_Api_Legalforms extends Shineisp_Api_Abstract_Action  {
    
    public function getAll() {
        $this->authenticate();
        
        return Legalforms::getList();
    }
    
}