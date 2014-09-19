COMPILE      = box build
TARGET       = dist/epilog.phar
OUTPUT       = dist/epilog
DELETE_FILE  = rm -f
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
	./$(OUTPUT) . --pretend

clean:
	$(DEL_FILE) $(OUTPUT)

install:
	$(CP_FILE) $(OUTPUT) $(INSTALL_PATH)
