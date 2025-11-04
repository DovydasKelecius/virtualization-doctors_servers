#!/bin/bash
# ------------------------------------------------------------
# ansible-vm initialization script (OpenNebula 4.14 compatible)
# Installs Ansible, Python, OpenNebula CLI tools, SSH, sudo user
# ------------------------------------------------------------

LOG="/var/log/ansible-init.log"
exec > >(tee -a $LOG) 2>&1
set -eux

export DEBIAN_FRONTEND=noninteractive

echo "[*] Updating system packages..."
apt-get update -y
apt-get install -y software-properties-common curl wget gnupg lsb-release git jq sshpass sudo python3-pip

echo "[*] Installing Ansible..."
apt-get install -y ansible

echo "[*] Installing OpenNebula CLI tools..."
apt-get install -y opennebula-tools || true

# ------------------------------------------------------------
# Create 'ansible' user with sudo privileges
# ------------------------------------------------------------
if ! id ansible &>/dev/null; then
    echo "[*] Creating ansible user..."
    useradd -m -s /bin/bash ansible
    echo "ansible:ansible" | chpasswd
    usermod -aG sudo ansible
fi

# Passwordless sudo
echo "ansible ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/ansible
chmod 440 /etc/sudoers.d/ansible

# ------------------------------------------------------------
# Set hostname
# ------------------------------------------------------------
hostnamectl set-hostname ansible-vm

# ------------------------------------------------------------
# Set up SSH key from context (if provided)
# ------------------------------------------------------------
if [ -f /mnt/context/authorized_keys ]; then
    echo "[*] Configuring SSH key for ansible user..."
    mkdir -p /home/ansible/.ssh
    cp /mnt/context/authorized_keys /home/ansible/.ssh/authorized_keys
    chmod 600 /home/ansible/.ssh/authorized_keys
    chown -R ansible:ansible /home/ansible/.ssh
fi

# ------------------------------------------------------------
# Optional: Preconfigure OpenNebula credentials (if env vars set)
# ------------------------------------------------------------
if [ -n "$ONE_XMLRPC" ] && [ -n "$ONE_AUTH" ]; then
    mkdir -p /home/ansible/.one
    echo "$ONE_AUTH" > /home/ansible/.one/one_auth
    echo "export ONE_XMLRPC=$ONE_XMLRPC" >> /home/ansible/.bashrc
    echo "export ONE_AUTH=$ONE_AUTH" >> /home/ansible/.bashrc
    chown -R ansible:ansible /home/ansible/.one
fi

echo "Ansible VM initialization complete!"
