#!/usr/bin/env bash
set -euo pipefail
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"
version="$(sed -n 's/.*"version"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p' composer.json | head -n1)"
if [ -z "${version:-}" ]; then
  echo "version not found in composer.json" >&2
  exit 1
fi

echo "Select release type:"
echo "1) Release (tag: v${version})"
echo "2) Beta (tag: v${version}-beta)"
read -r -p "Choose [1/2]: " release_type

case "${release_type}" in
  1|release|Release|RELEASE)
    tag="v${version}"
    tag_message="Release ${version}"
    is_beta="0"
    ;;
  2|beta|Beta|BETA)
    tag="v${version}-beta"
    tag_message="Beta ${version}"
    is_beta="1"
    ;;
  *)
    echo "Invalid choice." >&2
    exit 1
    ;;
esac

if git rev-parse -q --verify "refs/tags/${tag}" >/dev/null; then
  echo "local tag ${tag} already exists"
  echo "deleting local tag ${tag}..."
  git tag -d "${tag}"
fi

if git ls-remote --tags origin "${tag}" | grep -q "${tag}"; then
  echo "remote tag ${tag} already exists on origin"
  echo "deleting remote tag ${tag}..."
  if [ "${is_beta}" = "1" ]; then
    git push -o ci.skip origin ":refs/tags/${tag}"
  else
    git push origin ":refs/tags/${tag}"
  fi
fi

git tag -a "${tag}" -m "${tag_message}"
echo "created tag ${tag}"

read -r -p "Push tag ${tag} to origin? [Y/n] " push_response
push_response="${push_response:-Y}"
if [[ "$push_response" =~ ^([nN][oO]?|[nN])$ ]]; then
  echo "skip pushing tag"
  exit 0
fi

if [ "${is_beta}" = "1" ]; then
  git push -o ci.skip origin "${tag}"
else
  git push origin "${tag}"
fi
echo "pushed tag ${tag} to origin"
