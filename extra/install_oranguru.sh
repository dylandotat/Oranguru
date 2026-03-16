#!/bin/bash

shopt -s dotglob  # Bash-specific option

set -e

print_help() {
  echo -e "\033[0;35m$1\033[0m"
}

print_error() {
  echo -e "\033[1;31mError:\033[0m $1"
}

require_user_approval() {
  echo "Do you want to continue? (y/n)"
  read -r response
  if [ "$response" != "y" ] && [ "$response" != "Y" ]; then
    exit 1
  fi
}

branch="main"
protected="$PWD"  # Non-public directory for the source and database

# Check which public directory we should use
public="$1"
if [ -z "$public" ]; then
  print_error "no public directory specified"
  exit 2
fi
if [ ! -d "$public" ]; then
  print_error "directory '$public' does not exist"
  exit 1
fi
if [ ! "$2" = "--overwrite" ] && [ -n "$(find -L "$public" -mindepth 1 -print -quit)" ]; then
  echo "Public directory '$public' is not empty"
  require_user_approval
fi

# Fetch the source
if [ ! "$2" = "--overwrite" ] && [ -d "oranguru" ]; then
  echo "Source code directory '$protected/oranguru' already exists"
  require_user_approval
fi
print_help "Downloading source code…"
wget -O oranguru.zip "https://github.com/dwilding/oranguru/archive/refs/heads/$branch.zip"
rm -rf oranguru
unzip oranguru.zip -d oranguru
rm oranguru.zip

# Build the server and prepare the public directory
print_help "Installing server…"
cd "$protected/oranguru"
mv oranguru-$branch/app oranguru-$branch/server .
"oranguru-$branch/scripts/build_server.sh" "$protected/oranguru.db" "$protected/oranguru.toml"
rm -rf "oranguru-$branch"
rm -rf app
rm -rf "$public"/*
mv server/public/* "$public"
print_help "Done!"
