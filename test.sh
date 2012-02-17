reset

echo "Test Pimunit"
echo "-------------------------"
echo ""
phpunit --bootstrap bootstrap.php --verbose tests/tests/

echo ""
echo "Test lib/anyDi"
echo "-------------------------"
echo ""
cd lib/Any
phpunit
cd ../..

