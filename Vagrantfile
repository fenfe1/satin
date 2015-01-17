VAGRANTFILE_API_VERSION = "2"

# Project Settings
  hostname_base = "calcifer"
  hostname_project = "satin"
  hostname_environment = "dev"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # Applies to all VMs
    config.vm.box = "antarctica/trusty"

    # SSH keys
      # Due to a change[1] in Vagrant the default insecure private key is now replaced by a new random key,
      # whilst this is good from a security point of view it doesn't work with provisioner's easily, or
      # in cases where multiple VMs are defined at all, as each machine is given its own random key.
      # [1] https://github.com/mitchellh/vagrant/issues/2608
      config.ssh.insert_key = false

    # Shared folders
      # /vagrant is not used to ensure consistency across environments [development/staging/production]
      config.vm.synced_folder "./", "/app"
      config.vm.synced_folder '.', '/vagrant', disabled: true

    # Network adapters
      config.vm.network "private_network", type: "dhcp"  # Define Networking

    # Automatic hostname registration
      config.hostmanager.enabled = true
      config.hostmanager.manage_host = true
      config.hostmanager.ignore_private_ip = false
      config.hostmanager.include_offline = true

  # VMs
  config.vm.define hostname_base + "-" + hostname_project + "-" + hostname_environment + "-" + "node1" do |vm1|

      vm1.vm.hostname = hostname_base + "-" + hostname_project + "-" + hostname_environment + "-" + "node1"  # Define hostname

      # Provision using ansible
      # Due to bug[1] in Vagrant this block MUST be in the LAST VM specified in this vagrantfile
      # [1] https://github.com/mitchellh/vagrant/issues/1784

        # Bootstrap - load required public keys
        vm1.vm.provision "ansible" do |ansible|

          # Standard configuration
            ansible.inventory_path = 'provisioning/development'
            ansible.limit = 'all'

          # Playbook specific configuration
            ansible.playbook = 'provisioning/bootstrap-vagrant.yml'
            ansible.raw_arguments = ['--private-key', '~/.vagrant.d/insecure_private_key']
        end

        # Setup infrastructure
        vm1.vm.provision "ansible" do |ansible|

          # Standard configuration
            ansible.inventory_path = 'provisioning/development'
            ansible.limit = 'all'

          # Playbook specific configuration
            ansible.playbook = 'provisioning/site-dev.yml'
        end
  end
end
