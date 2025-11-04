#!/usr/bin/env bash
set -e

# --- Configuration ---
VENV_DIR=".venv"
MASTER_PLAYBOOK="playbooks/deploy_ansible_vm.yml"
SETUP_PLAYBOOK="playbooks/setup_ansible_vm.yml"
INVENTORY_FILE="inventory.ini"
VAULT_ID_ARG1="sudo_pass_ansible@./vault_password.sh"
VAULT_ID_ARG2="vault_pass_ansible@./vault_password.sh"
PYTHON_INTERPRETER_PATH="$(pwd)/$VENV_DIR/bin/python3"
# ---------------------

echo "--- 1. Setting up Python Virtual Environment ---"
# ... (VENV setup and activation remains the same)
if [ ! -d "$VENV_DIR" ]; then
    python3 -m venv "$VENV_DIR"
fi

source "$VENV_DIR/bin/activate"

# Ensure pyone is installed
if ! pip list | grep -q pyone; then
    pip install pyone
fi
# ... (End VENV setup)

echo "--- 2. Running master playbook to deploy Ansible VM ---"
ansible-playbook "$MASTER_PLAYBOOK" \
    --vault-id "$VAULT_ID_ARG1" \
    --vault-id "$VAULT_ID_ARG2" \
    -e "ansible_python_interpreter=$PYTHON_INTERPRETER_PATH" \
    -e 'host_key_checking=False' \
    || { echo "Error: Deploy Ansible VM failed"; deactivate; exit 1; }

echo "--- 3. Setup ansible vm ---"
ansible-playbook -i "$INVENTORY_FILE" "$SETUP_PLAYBOOK" \
                --vault-id "$VAULT_ID_ARG1" \
                --vault-id "$VAULT_ID_ARG2" \
                -e 'ansible_python_interpreter=/usr/bin/python3'
                -e 'host_key_checking=False'
                || { echo "Error: Setup Ansible VM failed"; deactivate; exit 1; }

echo "--- 4. Ansible VM bootstrap complete ---"

deactivate