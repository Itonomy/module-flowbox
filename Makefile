TAG := $(shell git tag -l --contains origin/master)
PKG := itonomy_module-flowbox-$(TAG)
ZIP := $(PKG).zip

module:
	git checkout $(TAG)
	zip -qr $(ZIP) . --exclude=.git --exclude=.gitignore --exclude=.idea* --exclude=Makefile --exclude=*.DS_Store* --exclude=*.git*
	mkdir -p ./pkg/$(PKG)
	mv $(ZIP) ./pkg/$(PKG)/

.PHONY: clean

clean:
	rm -rf ./pkg
