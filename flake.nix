{
  description = "Better encoding conversion for PHP";

  inputs = {
    # For old PHP versions.
    phps.url = "github:fossar/nix-phps";
  };

  outputs = { self, phps }:
    let
      # nixpkgs is a repository with software packages and some utilities.
      # From simplicity, we inherit it from the phps flake.
      inherit (phps.inputs) nixpkgs;

      # Configure the development shell here (e.g. for CI).

      # By default, we use the default PHP version from Nixpkgs.
      matrix.phpPackage = "php";
      # Allow easily removing some extensions for testing.
      matrix.forbiddenExtensionNames = [
        # "iconv"
        # "mbstring"
      ];
    in
      let
        # We only support a single platform at the moment,
        # since our binary cache only contains PHP packages for that.
        system = "x86_64-linux";

        # Get Nixpkgs packages for current platform.
        pkgs = nixpkgs.legacyPackages.${system};

        # Create a PHP package from the selected PHP package.
        phpBase = phps.packages.${system}.${matrix.phpPackage};
        php =
          phpBase.withExtensions
            ({ all, enabled }:
              let
                # Get the forbidden extensions by name.
                forbiddenExtensions = pkgs.lib.attrVals matrix.forbiddenExtensionNames all;
              in
              builtins.filter (ext: !builtins.elem ext forbiddenExtensions) enabled
            );
      in {
        # Expose shell environment for development.
        devShell.${system} = pkgs.mkShell {
          nativeBuildInputs = [
            # Composer and PHP.
            php
            phpBase.packages.composer

            phpBase.packages.php-cs-fixer-2
          ];
        };
      };
}
