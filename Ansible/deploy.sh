#!/usr/bin/env bash
set -euo pipefail

VENV_DIR=".venv"
MASTER_PLAYBOOK="playbooks/deploy_ansible_vm.yml"
SETUP_PLAYBOOK="playbooks/setup_ansible_vm.yml"
INVENTORY_FILE="inventory.ini"
VAULT_PASS_SCRIPT="./vault_password.sh"

# Cleanup on exit
trap '[[ -n "${VENV_ACTIVATED:-}" ]] && deactivate 2>/dev/null || true' EXIT

echo "=== 1. Setting up Python Virtual Environment ==="
[ ! -d "$VENV_DIR" ] && python3 -m venv "$VENV_DIR"
source "$VENV_DIR/bin/activate"
VENV_ACTIVATED=1

pip install --upgrade pip --quiet
pip install --quiet pyone ansible
ansible-galaxy collection install community.general --force

echo "=== 2. Deploying Ansible VM ==="
# REMOVED the broken -e ansible_python_interpreter line
ansible-playbook "$MASTER_PLAYBOOK" \
    -u root \
    --vault-password-file "$VAULT_PASS_SCRIPT" \
    -e 'host_key_checking=False' \
    || { echo "ERROR: Deploy failed"; exit 1; }

echo "=== 3. Configuring Ansible VM ==="
ansible-playbook -i "$INVENTORY_FILE" "$SETUP_PLAYBOOK" \
    -u root \
    --vault-password-file "$VAULT_PASS_SCRIPT" \
    -e 'ansible_python_interpreter=/usr/bin/python3' \
    -e 'host_key_checking=False' \
    || { echo "ERROR: Setup failed"; exit 1; }

echo "=== 4. All done! ==="