# Terraform definition file - this file is used to describe the required infrastructure for this project.


# Digital Ocean provider configuration

provider "digitalocean" {
	token = "${var.digital_ocean_token}"
}


# Resources

# 'calcifer-satin-prod-node1' resource

module "calcifer-satin-prod-node1" {
    source = "./droplet"
    hostname = "calcifer-satin-prod-node1"
    ssh_fingerprint = "${var.ssh_fingerprint}"
}

output "calcifer-satin-prod-node1-ip-v4-address" {
    value = "${module.calcifer-satin-prod-node1.ip_v4_address}"
}
