<?php

use PHPUnit\Framework\TestCase;

final class InfPagesTest extends TestCase
{

    /**
     * @runInSeparateProcess
     */
    public function testInfList()
    {
    	$expected_res = '{ "records": [{"id":"infid1","vms":"<a href=\'getvminfo.php?id=infid1&vmid=vmid1\' alt=\'VM Info\' title=\'VM Info\'>vmid1<br><a href=\'getvminfo.php?id=infid1&vmid=vmid2\' alt=\'VM Info\' title=\'VM Info\'>vmid2<br>","outputs":"<a href=\"getoutputs.php?id=infid1\">Show</a>","cont.Message":"<a href=\"getcontmsg.php?id=infid1\">Show</a>","status":"<span style=\'color:green\'>configuring</span>","reconfigure":"N/A","delete": "<a onclick=\"javascript:operateinf(\'destroy\', \'infid1\')\" href=\"#\"><img src=\"images/borrar.gif\" border=\"0\" alt=\"Delete\" title=\"Delete\"></a>","addResources":"<a href=\"form.php?id=infid1?>\"><img src=\"images/add_resources_icon.png\" border=\"0\" alt=\"Add Resources\" title=\"Add Resources\"></a>"}], "queryRecordCount": 1, "totalRecordCount": 1}';
   		$this->expectOutputString($expected_res);
        #$this->expectOutputRegex('/.*infid1.*/');
        #$this->expectOutputRegex("/.*style='color:green'>configuring.*/");
        #$this->expectOutputRegex("/.*getvminfo.php\?id=infid1&vmid=vmid1.*/");
        #$this->expectOutputRegex("/.*getvminfo.php\?id=infid1&vmid=vmid2.*/");
        $_SESSION = array("user"=>"admin", "password"=>"admin");

        $im = $this->getMockBuilder(IMXML::class)
            ->setMethods(['GetInfrastructureList', 'GetInfrastructureInfo', 'GetInfrastructureState'])
            ->getMock();
        $im->method('GetInfrastructureList')
            ->willReturn(array("infid1"));
        $res = array("state"=>"running", "vm_states"=>array("vmid1"=>"running","vmid2"=>"running"));
        $im->method('GetInfrastructureState')
            ->willReturn($res);

        $GLOBALS['mock_im'] = $im;

        include('../../list_json.php');
        unset($GLOBALS['mock_im']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetVMInfo()
    {
        $this->expectOutputRegex("/.*style='color:green'>configured.*/");
        $this->expectOutputRegex("/.*0 => 10.0.0.2<br>1 => 10.0.0.1.*/");
        $this->expectOutputRegex("/.*getcontmsg.php\?id=infid&vmid=vmid.*/");
        $_SESSION = array("user"=>"admin", "password"=>"admin");

        $radl = "" .
        "network publica (outbound = 'yes') " .
        "network privada ( ) " .
        "system front ( " .
        "state = 'configured' and " .
        "provider.type = 'EC2' and " .
        "cpu.arch = 'x86_64' and " .
        "cpu.count >= 1 and " .
        "memory.size >= 512m and " .
        "net_interface.1.connection = 'publica' and " .
        "net_interface.1.ip = '10.0.0.1' and " .
        "net_interface.0.connection = 'privada' and " .
        "net_interface.0.dns_name = 'front' and " .
        "net_interface.0.ip = '10.0.0.2' and " .
        "disk.0.os.flavour = 'centos' and " .
        "disk.0.os.version >='7' and " .
        "disk.0.os.name = 'linux' and " .
        "disk.0.applications contains (name = 'ansible.modules.grycap.octave') and " .
        "disk.0.applications contains (name = 'gmetad') and " .
        "disk.0.os.credentials.private_key = 'priv' and " .
        "disk.1.size = 1GB and " .
        "disk.1.device = 'hdb' and " .
        "disk.1.fstype = 'ext4' and " .
        "disk.1.mount_path = '/mnt/disk'" .
        ")";
        $im = $this->getMockBuilder(IMXML::class)
            ->setMethods(['GetVMInfo'])
            ->getMock();
        $im->method('GetVMInfo')
            ->willReturn($radl);

        $_GET = array('vmid'=>'vmid', 'id'=>'infid');
        $GLOBALS['mock_im'] = $im;

        include('../../getvminfo.php');
        unset($GLOBALS['mock_im']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetVMContMsg()
    {
        $this->expectOutputRegex("/.*vmcontmsg.*/");
        $_SESSION = array("user"=>"admin", "password"=>"admin");

        $im = $this->getMockBuilder(IMXML::class)
            ->setMethods(['GetVMContMsg','GetInfrastructureContMsg'])
            ->getMock();
        $im->method('GetVMContMsg')
            ->willReturn("vmcontmsg");
        $im->method('GetInfrastructureContMsg')
            ->willReturn("infcontmsg");

        $_GET = array('vmid'=>'vmid', 'id'=>'infid');
        $GLOBALS['mock_im'] = $im;

        include('../../getcontmsg.php');
        unset($GLOBALS['mock_im']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetContMsg()
    {
        $this->expectOutputRegex("/.*infcontmsg.*/");
        $_SESSION = array("user"=>"admin", "password"=>"admin");

        $im = $this->getMockBuilder(IMXML::class)
            ->setMethods(['GetVMContMsg','GetInfrastructureContMsg'])
            ->getMock();
        $im->method('GetVMContMsg')
            ->willReturn("vmcontmsg");
        $im->method('GetInfrastructureContMsg')
            ->willReturn("infcontmsg");

        $_GET = array('id'=>'infid');
        $GLOBALS['mock_im'] = $im;

        include('../../getcontmsg.php');
        unset($GLOBALS['mock_im']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetOutputs()
    {
        $this->expectOutputRegex("/.*infcontmsg.*/");
        $this->expectOutputRegex('/.*<td>[ \n\\n\t]+key[ \n\\n\t]+<\/td>[ \n\\n\t]*<td>[ \n\\n\t]+value[ \n\\n\t]+<\/td>.*/');
        $_SESSION = array("user"=>"admin", "password"=>"admin");

        $im = $this->getMockBuilder(IMRest::class)
            ->setMethods(['GetOutputs'])
            ->getMock();
        $im->method('GetOutputs')
            ->willReturn(array("key"=>"value"));

        $_GET = array('id'=>'infid');
        $GLOBALS['mock_im'] = $im;

        include('../../getoutputs.php');
        unset($GLOBALS['mock_im']);
    }
}
?>

