#!/bin/bash

# SuperClaude Installer Script
# Installs SuperClaude configuration framework to enhance Claude Code

set -e  # Exit on error

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}SuperClaude Installer${NC}"
echo "======================"
echo ""

# Check if we're in SuperClaude directory
if [ ! -f "CLAUDE.md" ] || [ ! -d ".claude/commands" ]; then
    echo -e "${RED}Error: This script must be run from the SuperClaude directory${NC}"
    echo "Please cd into the SuperClaude directory and try again."
    exit 1
fi

# Check if existing .claude directory exists
if [ -d "$HOME/.claude" ]; then
    echo -e "${YELLOW}Existing Claude configuration found${NC}"
    echo -n "Backup existing configuration? (y/n): "
    read -r backup_choice
    if [ "$backup_choice" = "y" ]; then
        # Create backup directory inside .claude
        backup_dir="$HOME/.claude/backup.$(date +%Y%m%d_%H%M%S)"
        mkdir -p "$backup_dir"
        
        # Backup ALL existing files
        echo "Backing up all existing files..."
        
        # Copy everything except backup directories
        for item in "$HOME/.claude"/*; do
            basename_item=$(basename "$item")
            # Skip backup directories to avoid nested backups
            if [[ ! "$basename_item" =~ ^backup\. ]]; then
                if [ -e "$item" ]; then
                    cp -r "$item" "$backup_dir/"
                fi
            fi
        done
        
        echo -e "${GREEN}Backed up existing files to: $backup_dir${NC}"
    fi
fi

echo ""
echo "Installing SuperClaude..."

# Create directory structure
echo "Creating directories..."
mkdir -p "$HOME/.claude/commands/shared"

# Copy main configuration files
echo "Copying configuration files..."
cp CLAUDE.md RULES.md PERSONAS.md MCP.md "$HOME/.claude/"

# Copy command files
echo "Copying slash commands..."
cp .claude/commands/*.md "$HOME/.claude/commands/" 2>/dev/null || true

# Copy shared resources
echo "Copying shared resources..."
cp .claude/commands/shared/*.yml "$HOME/.claude/commands/shared/"

# Verify installation
echo ""
echo "Verifying installation..."

# Count installed files
main_files=$(ls -1 "$HOME/.claude/"*.md 2>/dev/null | wc -l)
command_files=$(ls -1 "$HOME/.claude/commands/"*.md 2>/dev/null | wc -l)
shared_files=$(ls -1 "$HOME/.claude/commands/shared/"*.yml 2>/dev/null | wc -l)

echo -e "Main config files: ${GREEN}$main_files${NC} (expected: 4)"
echo -e "Command files: ${GREEN}$command_files${NC} (expected: 18)"
echo -e "Shared resources: ${GREEN}$shared_files${NC} (expected: 26)"

# Check if installation was successful
if [ "$main_files" -ge 4 ] && [ "$command_files" -ge 18 ] && [ "$shared_files" -ge 26 ]; then
    echo ""
    echo -e "${GREEN}✓ SuperClaude installed successfully!${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Open any project with Claude Code"
    echo "2. Try a command: /user:analyze --code"
    echo "3. Activate a persona: /persona:architect"
    echo ""
    if [ -n "$backup_dir" ] && [ -d "$backup_dir" ]; then
        echo -e "${YELLOW}Note: Your previous configuration was backed up to:${NC}"
        echo "$backup_dir"
        echo ""
    fi
    echo "For more information, see README.md"
else
    echo ""
    echo -e "${RED}✗ Installation may be incomplete${NC}"
    echo "Please check the error messages above or install manually."
    echo "See README.md for manual installation instructions."
    exit 1
fi