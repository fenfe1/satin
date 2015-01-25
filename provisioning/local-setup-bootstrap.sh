#!/bin/bash

# Very simple script to download the latest version of: https://gist.github.com/felnne/9b584c5d3a9f765dbe18
#
# Author: Felix Fennell <felnne@bas.ac.uk> -- Web & Applications Team - British Antarctic Survey
# Version: 0.1.0

# Script vars
project_dir=$(pwd)
file="provisioning/local-setup.sh";

# Delete file if it already exists
if [ -f "$file" ]
then
	rm "$file"
fi

# Download file
curl -L -o "$file" https://gist.github.com/felnne/9b584c5d3a9f765dbe18/raw

# Check file exists
if [ -f "$file" ]
then

    # Set script as executable
    chmod +x "$file"

    # Check file is excutable
    if [[ -x "$file" ]]
    then

        # Execute file
        /bin/bash ${file} "$project_dir"
	    exit 0
    else
        printf "\n"
        echo "Execute permissions were not found on [$file] - check for errors and retry."
        exit 1
    fi
else
    printf "\n"
	echo "Could not find: [$file] - check for downloading errors and retry."
	exit 1
fi
