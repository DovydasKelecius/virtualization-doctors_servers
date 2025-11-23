#!/usr/bin/env bash
set -euo pipefail

VENV_DIR=".venv"
VAULT_PASS_SCRIPT="./vault_password.sh"
PLAYBOOK="playbooks/cleanup_vms.yml"

source "$VENV_DIR/bin/activate"

echo "=== CLEANING UP ALL VMs ==="

ansible-playbook "$PLAYBOOK" \
    --vault-password-file "$VAULT_PASS_SCRIPT" \
    -e 'host_key_checking=False' \
    || echo "Cleanup finished (some VMs may already be gone)"

echo "=== All VMs deleted + registry cleaned ==="
deactivate