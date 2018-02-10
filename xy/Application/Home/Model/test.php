class TestModelTest extends PHPUnit_Extensions_Database_TestCase{
/**
* return PHPUnit_Extensions_Database_DB_IDatabaseConnection
*/
public function getConnection()
{
$pdo = new PDO('mysql:host=localhost;dbname=test','root','root');
return $this->createDefaultDBConnection($pdo, 'test');
}

/**
* return PHPUnit_Extensions_Database_DataSet_IDataSet
*/
public function getDataSet()
{
return $this->createXMLDataSet(dirname(__FILE__).'/db.xml');
}

public function testGetValue()
{
//启动thinkphp
define('APP_PATH', dirname(__FILE__).'/../../../Application/');
//设置标志，防止程序启动，只要加载了框架和类就行
define('APP_PHPUNIT', true);
// 引入ThinkPHP入口文件
require_once dirname(__FILE__).'/../../../ThinkPHP/ThinkPHP.php';
$testModel=new \Home\Model\TestModel();
$this->assertEquals(2,$testModel->getValue(1));
}
}