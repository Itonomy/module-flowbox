SRCDIR=./src
PKGDIR=./package

package:
	rm -rf $(PKGDIR)
	mkdir -p $(PKGDIR)
	cp composer.json LICENSE.md README.md $(PKGDIR)/
	cp -R $(SRCDIR)/* $(PKGDIR)/
	zip -r itonomy_module-flowbox-1.0.2.zip $(PKGDIR)/
	rm -rf $(PKGDIR)/*
	mv itonomy_module-flowbox-1.0.2.zip $(PKGDIR)/

.PHONY: clean

clean:
	rm -rf $(PKGDIR)
