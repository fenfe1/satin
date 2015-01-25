# Satin

Example implementation of the BAS People API (version 1).

## TODO:

* Move to Felnne repository and update `composer.json` namespace

## Getting started

### Requirements

You will need the following local software depending on the environment you wish to target:

#### All environments

* Mac OS X
* [Ansible](http://www.ansible.com) `brew install ansible`
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

#### Development (local)

* [VMware Fusion](http://vmware.com/fusion) `brew cask install vmware-fusion`
* [Vagrant](http://vagrantup.com) `brew cask install vagrant`
* [Host manager](https://github.com/smdahlen/vagrant-hostmanager) and [Vagrant VMware](http://www.vagrantup.com/vmware) plugins `vagrant plugin install vagrant-hostmanager && vagrant plugin install vagrant-vmware-fusion`


VMs are managed using Vagrant and configured by Ansible.
* [Terraform](https://www.terraform.io) `brew cask install terraform`
* [A Digital Ocean Personal Access Token](https://www.digitalocean.com/community/tutorials/how-to-use-the-digitalocean-api-v2) (with read/write permissions) in the Digital Ocean account [1]
* Your public key listed in the Digital Ocean account [1]

#### Setup
[1] Specifically the `felix@felixfennell.co.uk` account.

```shell
$ git clone git@github.com:fenfe1/satin.git
$ cd satin

$ provisioning/local-setup-bootstrap.sh
$ vagrant up  ([1])

$ ssh calcifer-satin-dev-node1
$ cd /app
$ composer install

$ logout
```

#### Usage
[1] You will be asked to provide your API user account username and password to create a configuration file.


```shell
$ git clone git@github.com:fenfe1/satin.git
$ cd satin

$ provisioning/local-setup-bootstrap.sh

$ nano terraform.tfvars  (see [1])
$ terraform get
$ terraform apply
```

DNS changes are currently manual, use the [Digital Ocean control panel](https://cloud.digitalocean.com) to point:
 
* An `A` record for `calcifer-statin-prod-web1.calcifer.co` set to the value of the `calcifer-satin-prod-node1-ip-v4-address`  Terraform output.

```shell
$ ansible-playbook -i provisioning/production provisioning/bootstrap-digital-ocean.yml
$ ansible-playbook -i provisioning/production provisioning/site-prod.yml  ([2])

$ ssh calcifer-satin-prod-node1.calcifer.co
$ cd /app

$ composer install

$ logout
```

[1] This file should be populated with your Digital Ocean Personal Access Token and the fingerprint of your SSH public key, using `ssh-keygen -lf ~/.ssh/id_rsa.pub | awk '{print $2}' | pbcopy`, as per this example.

```javascript
digital_ocean_token = "TOKEN"
ssh_fingerprint = "FINGERPRINT"
```

To demo the People API methods use the provided demo script.

```shell
$ ssh calcifer-satin-dev-node1
$ cd /app/src

$ php satin.php

$ logout
```
