TAG = `git tag -l --contains origin/master`
PKG = itonomy_module-flowbox-$(TAG)
ZIP = $(PKG).zip

$(PKG):
	git checkout $(TAG)
	rm -rf $(PKG)
	mkdir -p $(PKG)
	cp composer.json LICENSE.md README.md $(PKG)/
	cp -R ./src/* $(PKG)/
	zip -r $(ZIP) $(PKG)
	rm -rf $(PKG)/*
	mv $(ZIP) $(PKG)/
	mv $(PKG) ./pkg

.PHONY: clean

clean:
	rm -rf ./pkg
