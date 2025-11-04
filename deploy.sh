#!/usr/bin/env bash
set -e

# --- Configuration ---
VENV_DIR=".venv"
PLAYBOOK_FILE="deploy_playbook.yml"
VAULT_ID_ARG1="sudo_pass_ansible@./vault_password.sh"
VAULT_ID_ARG2="vault_pass_ansible@./vault_password.sh"
PYTHON_INTERPRETER_PATH="$(pwd)/$VENV_DIR/bin/python3"
MITOGEN_PATH="$(pwd)/mitogen-0.3.30/ansible_mitogen/plugins/strategy"
# ---------------------

echo "--- 1. Setting up Python Virtual Environment ---"

# Create the venv if it doesn't exist
if [ ! -d "$VENV_DIR" ]; then
    echo "Creating virtual environment '$VENV_DIR'..."
    python3 -m venv "$VENV_DIR" || { echo "Error: Failed to create venv."; exit 1; }
fi

# Activate the venv
source "$VENV_DIR/bin/activate"

# Check if pyone is installed
if ! pip list | grep -q "pyone"; then
    echo "Installing 'pyone' dependency..."
    pip install pyone || { echo "Error: Failed to install pyone."; deactivate; exit 1; }
else
    echo "'pyone' is already installed."
fi

echo "--- 2. Checking for Ansible Inventory and Config ---"

# Check if ansible.cfg exists (optional but recommended for production)
if [ ! -f ".ansible.cfg" ]; then
    echo "Creating basic ansible.cfg..."
    echo "[defaults]" > ansible.cfg
    echo "interpreter_python = $PYTHON_INTERPRETER_PATH" >> ansible.cfg
    echo "host_key_checking = False" >> ansible.cfg
    echo "strategy_plugins = $MITOGEN_PATH" >> ansible.cfg
    echo "strategy = mitogen_linear" >> ansible.cfg
fi

echo "--- 3. Running Ansible Playbook ---"

# Execute the playbook, using both vault IDs
ansible-playbook "$PLAYBOOK_FILE" \
    --vault-id "$VAULT_ID_ARG1" \
    --vault-id "$VAULT_ID_ARG2" \
    -e "ansible_python_interpreter=$PYTHON_INTERPRETER_PATH" \
    || { echo "Error: Ansible playbook failed."; deactivate; exit 1; }

echo "--- 4. Deployment Complete ---"

# Deactivate the environment upon success
deactivate

echo "Successfully completed deployment of $PLAYBOOK_FILE."
