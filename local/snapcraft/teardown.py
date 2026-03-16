#!/usr/bin/env python

from pathlib import Path
import os

import oranguru


def main():
    ports_path = Path(os.getenv("SNAP_COMMON")) / "ports"
    ports = ports_path.read_text().splitlines()
    ports_path.write_text("")
    for port in ports:
        try:
            oranguru.stop(int(port))
        except oranguru.WrongAppError:
            pass


if __name__ == "__main__":
    main()
