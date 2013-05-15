<?
class Shineisp_Api_Shineisp_Legalforms extends Shineisp_Api_Shineisp_Abstract_Action  {
    
    public function getAll() {
        $this->authenticate();
        
        return Legalforms::getList();
    }
    
}