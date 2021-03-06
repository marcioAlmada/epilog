COMPILE      = box build
TARGET       = bin/epilog.phar
OUTPUT       = bin/epilog
DELETE_FILE  = rm
CHMOD_FILE   = chmod +x
MOVE_FILE    = mv
CP_FILE      = cp
INSTALL_PATH = /usr/local/bin

default:
	$(COMPILE)
	$(MOVE_FILE) $(TARGET) $(OUTPUT)
	$(CHMOD_FILE) $(OUTPUT)

run:
	./$(OUTPUT) --help

pretend:
	./$(OUTPUT) pretend

clean:
	$(DELETE_FILE) $(OUTPUT)

install:
	$(CP_FILE) $(OUTPUT) $(INSTALL_PATH)
