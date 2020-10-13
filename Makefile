SRCDIR=./src
COMMIT=`git rev-parse --short master`
TAG=`git tag -l --contains $(COMMIT) | head -n 1`
PKGDIR=./package/$(TAG)

package:
	git checkout $(TAG)
	rm -rf $(PKGDIR)
	mkdir -p $(PKGDIR)
	cp composer.json LICENSE.md README.md $(PKGDIR)/
	cp -R $(SRCDIR)/* $(PKGDIR)/
	zip -r itonomy_module-flowbox-$(TAG).zip $(PKGDIR)/
	rm -rf $(PKGDIR)/*
	mv itonomy_module-flowbox-$(TAG).zip $(PKGDIR)/

.PHONY: clean

clean:
	rm -rf $(PKGDIR)
