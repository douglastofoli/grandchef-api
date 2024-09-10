{pkgs, ...}:
with pkgs; let
  projectName = "grandchef-api";

  php = pkgs.php83.buildEnv {
    extensions = {
      enabled,
      all,
    }:
      enabled ++ (with all; [xdebug php83Extensions.pgsql]);

    extraConfig = ''
      memory_limit=2G
      xdebug.mode=develop,debug,coverage
    '';
  };
in
  mkShell {
    name = "${projectName}-shell";

    packages = [php phpunit php83Packages.composer];
  }
