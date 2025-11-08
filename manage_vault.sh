#!/usr/bin/env bash
# manage_vault.sh
# Manage Ansible Vault files using a single vault password script and Vault IDs

VAULT_PASS_FILE="./vault_password.sh"

usage() {
  echo "Usage: $0 <vault-id> {encrypt|decrypt|view|edit|rekey} <file>"
  echo "  encrypt - Encrypts a plaintext file"
  echo "  decrypt - Decrypts an encrypted file (outputs to stdout)"
  echo "  view    - Displays contents without editing"
  echo "  edit    - Opens the vault file for editing"
  echo "  rekey   - Changes the vault password (uses vault_password.sh)"
  exit 1
}

if [ $# -lt 3 ]; then
  usage
fi

ID=$1
ACTION=$2
FILE=$3

if [ ! -x "$VAULT_PASS_FILE" ]; then
  echo "Error: vault password script not found or not executable ($VAULT_PASS_FILE)"
  exit 1
fi

if [ ! -f "$FILE" ] && [ "$ACTION" != "encrypt" ]; then
  echo "Error: target file '$FILE' does not exist."
  exit 1
fi

case "$ACTION" in
  encrypt)
    ansible-vault encrypt "$FILE" --vault-id "${ID}@${VAULT_PASS_FILE}"
    ;;
  decrypt)
    ansible-vault decrypt "$FILE" --vault-id "${ID}@${VAULT_PASS_FILE}"
    ;;
  view)
    ansible-vault view "$FILE" --vault-id "${ID}@${VAULT_PASS_FILE}"
    ;;
  edit)
    ansible-vault edit "$FILE" --vault-id "${ID}@${VAULT_PASS_FILE}"
    ;;
  rekey)
    ansible-vault rekey "$FILE" --vault-id "${ID}@${VAULT_PASS_FILE}"
    ;;
  *)
    usage
    ;;
esac
