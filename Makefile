# Silence output slightly
# .SILENT:

DB := dhil_dm
PROJECT := dm



include etc/Makefile

## Local make file

# Override any of the options above by copying them to makefile.local
-include Makefile.local

## -- No targets yet
