# Satin

Example implementation of the BAS People API (version 1).

## TODO:

* Move to Felnne repository and update `composer.json` namespace

## Getting started

### Requirements

You will need to the following local software depending on the environment you wish to target:

#### All environments

* Mac OS X
* [Ansible](http://www.ansible.com) `brew install ansible`

#### Development (local)

  * [VMware Fusion](http://vmware.com/fusion) `brew cask install vmware-fusion`
  * [Vagrant](http://vagrantup.com) `brew cask install vagrant`
  * [Host manager](https://github.com/smdahlen/vagrant-hostmanager) and [Vagrant VMware](http://www.vagrantup.com/vmware) plugins `vagrant plugin install vagrant-hostmanager && vagrant plugin install vagrant-vmware-fusion`
  * You have a private key `id_rsa` and public key `id_rsa.pub` in `~/.ssh/`
  * You have an entry like [1] in your `~/.ssh/config`

[1] SSH config entry

```shell
Host calcifer-*
    ForwardAgent yes
    User app
    IdentityFile ~/.ssh/id_rsa
    Port 22
```

### Local/Development

VMs are managed using Vagrant and configured by Ansible.

#### Setup

```shell
$ git clone git@github.com:fenfe1/satin.git
$ satin/provisioning/local_setup.sh satin

$ cd satin
$ vagrant up

$ ssh calcifer-satin-dev-node1
$ cd /app
$ composer install

$ logout
```

#### Usage

Create a `.secret_credentials.json` file in `/` and populate with your API user account credentials.

```json
{
    "username": "USERNAME",
    "password": "PASSWORD"
}
```

To demo the People API methods use the provided demo script.

```shell
$ ssh calcifer-satin-dev-node1
$ cd /app

$ cd /app/src
$ php satin.php

$ logout
```
