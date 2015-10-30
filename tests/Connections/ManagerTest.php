<?php

namespace Adldap\Tests\Connections;

use Adldap\Connections\Configuration;
use Adldap\Connections\Ldap;
use Adldap\Connections\Manager;
use Adldap\Tests\UnitTestCase;

class ManagerTest extends UnitTestCase
{
    public function testConstruct()
    {
        $m = new Manager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Connections\ConnectionInterface', $m->getConnection());
        $this->assertInstanceOf('Adldap\Connections\Configuration', $m->getConfiguration());
    }

    public function testAuthUsernameFailure()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = new Manager($connection, new Configuration());

        $this->setExpectedException('Adldap\Exceptions\Auth\UsernameRequiredException');

        $m->auth()->attempt(' ', 'password');
    }

    public function testAuthPasswordFailure()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = new Manager($connection, new Configuration());

        $this->setExpectedException('Adldap\Exceptions\Auth\PasswordRequiredException');

        $m->auth()->attempt('username', ' ');
    }

    public function testAuthFailure()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('connect')->once()->andReturn(true);
        $connection->shouldReceive('setOption')->twice()->andReturn(true);
        $connection->shouldReceive('isUsingSSL')->once()->andReturn(false);
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('bind')->once()->withArgs(['username', 'password'])->andReturn(false);
        $connection->shouldReceive('getLastError')->once()->andReturn('');
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = new Manager($connection, new Configuration());

        $this->assertFalse($m->auth()->attempt('username', 'password'));
    }

    public function testAuthPassesWithRebind()
    {
        $config = new Configuration();

        $config->setAdminUsername('test');
        $config->setAdminPassword('test');

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('connect')->once()->andReturn(true);
        $connection->shouldReceive('setOption')->twice()->andReturn(true);
        $connection->shouldReceive('isUsingSSL')->once()->andReturn(false);
        $connection->shouldReceive('isBound')->once()->andReturn(true);

        // Authenticates as the user
        $connection->shouldReceive('bind')->once()->withArgs(['username', 'password'])->andReturn(true);

        // Re-binds as the administrator
        $connection->shouldReceive('bind')->once()->withArgs(['test', 'test'])->andReturn(true);
        $connection->shouldReceive('getLastError')->once()->andReturn('');
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = new Manager($connection, $config);

        $this->assertTrue($m->auth()->attempt('username', 'password'));
    }

    public function testAuthPassesWithoutRebind()
    {
        $config = new Configuration();

        $config->setAdminUsername('test');
        $config->setAdminPassword('test');

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('connect')->once()->andReturn(true);
        $connection->shouldReceive('setOption')->twice()->andReturn(true);
        $connection->shouldReceive('isUsingSSL')->once()->andReturn(false);
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('bind')->once()->withArgs(['username', 'password'])->andReturn(true);
        $connection->shouldReceive('getLastError')->once()->andReturn('');
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = new Manager($connection, $config);

        $this->assertTrue($m->auth()->attempt('username', 'password', true));
    }

    public function testGroups()
    {
        $m = new Manager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Scopes\Groups', $m->groups());
    }

    public function testUsers()
    {
        $m = new Manager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Scopes\Users', $m->users());
    }

    public function testContainers()
    {
        $m = new Manager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Scopes\Containers', $m->containers());
    }

    public function testContacts()
    {
        $m = new Manager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Scopes\Contacts', $m->contacts());
    }

    public function testExchangeServers()
    {
        $m = new Manager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Scopes\ExchangeServers', $m->exchangeServers());
    }

    public function testComputers()
    {
        $m = new Manager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Scopes\Computers', $m->computers());
    }

    public function testOus()
    {
        $m = new Manager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Scopes\Contacts', $m->contacts());
    }

    public function test()
    {
        $m = new Manager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Scopes\Contacts', $m->contacts());
    }

    public function testPrinters()
    {
        $m = new Manager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Scopes\Printers', $m->printers());
    }

    public function testSearch()
    {
        $m = new Manager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Search\Factory', $m->search());
    }
}
