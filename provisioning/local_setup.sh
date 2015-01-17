#!/bin/bash

# This is a basic setup script developed mostly because i'm lazy.

# Script vars
project_dir=$1;
internal=1;  # Assume use of internal resources
internal_label=yes;
trash=0;  # Assume trash command isn't available

# Colour vars
Cyan='\033[0;36m'
Yellow='\033[0;33m'
Green='\033[0;32m'
NC='\033[0m'  # No colour

# Determine if internal resources are available - this test is much quicker if nmap is available
if hash nmap 2>/dev/null; then

	nmap -sP --max-retries=1 --host-timeout=1500ms stash.ceh.ac.uk | grep '1 host up' &> /dev/null
	if $? == 0; then

		printf "\n"
		echo -e "NMap can find stash"

		internal=1;
		internal_label=yes;
	else

		printf "\n"
		echo -e "NMap is unable to find stash"

		internal=0;
		internal_label=no;
	fi
else
	# Fallback to ping
	if ping -c 1 stash.ceh.ac.uk &> /dev/null; then

		internal=1;
		internal_label=yes;
	else

		internal=0;
		internal_label=no;
	fi
fi

# Determine if 'trash' command is available for less destructive deletions
if hash trash 2>/dev/null; then
	trash=1;
else
	# Fall back to rm -rf
	trash=0;
fi

# Setup
printf "\n"
echo -e "${Cyan}Basic provisioning setup script${NC}"

echo -e "- project directory is: ${Yellow}${project_dir}${NC}"
echo -e "- internal resources available: ${Yellow}${internal_label}${NC}"

# Copy SSH public key
printf "\n"
echo -e "[1/2] - Copying ${Yellow}~/.ssh/id_rsa.pub${NC} to ${Yellow}${project_dir}/provisioning/public_keys/${NC}"
cp ~/.ssh/id_rsa.pub "${project_dir}/provisioning/public_keys/"
echo -e "      - ${Green}Done${NC}"

# Clean ansible roles directory (i.e. delete everything inside)
printf "\n"
echo -e "[2/3] - Removing existing ansible roles in ${Yellow}${project_dir}/provisioning/roles${NC}"

if [ ${trash} -eq 1 ]; then
	trash ${project_dir}/provisioning/roles/*/
else
	rm -rf ${project_dir}/provisioning/roles/*/
fi

echo -e "      - ${Green}Done${NC}"

# Get ansible roles
printf "\n"

if [ ${internal} -eq 1 ]; then
	echo -e "[3/3] - Downloading ansible roles specified in ${Yellow}${project_dir}/ansible_roles.yml${NC}"
	ansible-galaxy install --role-file="${project_dir}/ansible_roles.yml" --roles-path="${project_dir}/provisioning/roles" --no-deps
else
	echo -e "[3/3] - Downloading ansible roles specified in ${Yellow}${project_dir}/ansible_roles_public.yml${NC}"
	ansible-galaxy install --role-file="${project_dir}/ansible_roles_public.yml" --roles-path="${project_dir}/provisioning/roles" --no-deps
fi

echo -e "      - ${Green}Done${NC}"

# Leaving message
printf "\n"
echo -e "${Cyan}Basic provisioning setup script${NC} - ${Green}Finished${NC}"
