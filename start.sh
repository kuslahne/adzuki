#!/usr/bin/env bash
nix-shell -p php81 php81Packages.composer symfony-cli

composer run-script start
