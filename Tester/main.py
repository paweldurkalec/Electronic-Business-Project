from selenium import webdriver
import time

from selenium.webdriver.common.by import By

PATH = "./chromedriver"
options = webdriver.ChromeOptions()
options.add_argument('--ignore-ssl-errors=yes')
options.add_argument('--ignore-certificate-errors')
driver = webdriver.Chrome(PATH, options=options)

NAME = 'Jan'
SURNAME = 'Kowalski'
EMAIL = 'pdurkalec1006@gmail.com'
PASSWORD = 'examplarypassword'
BIRTDATE = '1970-05-31'
ADDRES = "Politechniczna 0"
POST_CODE = '80-288'
CITY = 'Gdansk'

MODE='SLEEP'
SLEEP_TIME = 1.2

def custom_sleep():
    if MODE == 'SLEEP':
        time.sleep(SLEEP_TIME)

def login():
    driver.find_element(By.XPATH,'/html/body/main/header/nav/div/div/div[1]/div[2]/div[1]/div/a').click()
    driver.find_element(By.XPATH,
                        '/html/body/main/section/div/div/section/section/section'
                        '/form/section/div[1]/div[1]/input').send_keys(EMAIL)
    driver.find_element(By.XPATH,
                        '/html/body/main/section/div/div/section/section/section'
                        '/form/section/div[2]/div[1]/div/input').send_keys(PASSWORD)
    driver.find_element(By.XPATH,'//*[@id="submit-login"]').click()
    driver.find_element(By.XPATH, '/html/body/main/header/div[2]/div/div[1]/div[1]/a/img').click()


def register():

    #going to register from
    driver.find_element(By.XPATH, '/html/body/main/header/nav/div/div/div[1]/div[2]/div[1]/div/a').click()
    time.sleep(1)
    driver.find_element(By.XPATH, '/html/body/main/section/div/div/section/section/div/a').click()
    time.sleep(1)

    #register form
    driver.find_element(By.XPATH,'/html/body/main/section/div/div/section/section/section'
                                 '/form/section/div[1]/div[1]/label[1]').click()
    custom_sleep()
    name_textbox = driver.find_element(By.XPATH, '/html/body/main/section/div/div/section'
                                                 '/section/section/form/section/div[2]/div[1]/input')
    surname_textbox = driver.find_element(By.XPATH, '/html/body/main/section/div/div/section'
                                                    '/section/section/form/section/div[3]/div[1]/input')
    email_textbox = driver.find_element(By.XPATH, '/html/body/main/section/div/div/section'
                                                  '/section/section/form/section/div[4]/div[1]/input')
    password_textbox = driver.find_element(By.XPATH, '/html/body/main/section/div/div/section'
                                                     '/section/section/form/section/div[5]/div[1]/div/input')
    birthdate_textbox = driver.find_element(By.XPATH, '/html/body/main/section/div/div/section'
                                                      '/section/section/form/section/div[6]/div[1]/input')
    custom_sleep()
    name_textbox.send_keys(NAME)
    custom_sleep()
    surname_textbox.send_keys(SURNAME)
    custom_sleep()
    email_textbox.send_keys(EMAIL)
    custom_sleep()
    password_textbox.send_keys(PASSWORD)
    custom_sleep()
    birthdate_textbox.send_keys(BIRTDATE)
    driver.find_element(By.XPATH,'/html/body/main/section/div/div/section/section/section/form/section/div[8]/div[1]/span/label/input').click()
    custom_sleep()
    driver.find_element(By.XPATH,'/html/body/main/section/div/div/section/section/section/form/section/div[9]/div[1]/span/label/input').click()
    custom_sleep()

    driver.find_element(By.XPATH,'/html/body/main/section/div/div/section/section'
                                 '/section/form/footer/button').click()

def add_item_to_cart(XPATH, amount=1):
    #item
    driver.find_element(By.XPATH, XPATH).click()
    for i in range(0,amount-1):
        custom_sleep()
        driver.find_element(By.XPATH,
                            '/html/body/main/section/div/div/section/div[1]/div[2]/'
                            'div[2]/div[2]/form/div[2]/div/div[1]/div/span[3]/button[1]/i').click()

    custom_sleep()
    #add to cart
    driver.find_element(By.XPATH, '/html/body/main/section/div/div/section/div[1]/div[2]'
                                  '/div[2]/div[2]/form/div[2]/div').click()
    custom_sleep()
    #continue shopping
    driver.find_element(By.XPATH, '/html/body/div[1]/div/div/div[2]/div/div[2]/div/div/button').click()
    custom_sleep()
    driver.back()

def fill_cart():
    custom_sleep()
    #goto products
    driver.find_element(By.XPATH,'/html/body/main/header/div[2]/div/div[1]/div[2]/div[1]/ul/li/a').click()
    custom_sleep()
    #goto lawnmovers
    driver.find_element(By.XPATH,'/html/body/main/section/div/div[1]/div[1]/ul/li[2]/ul/li[1]/a').click()
    custom_sleep()
    #item 1
    add_item_to_cart('/html/body/main/section/div/div[2]/section/section/div[3]/div'
                     '/div[1]/div[1]/article/div/a/img', 2)
    #item 2
    add_item_to_cart('/html/body/main/section/div/div[2]/section/section/div[3]/div'
                     '/div[1]/div[5]/article/div/a/img', 3)
    #goto products
    driver.find_element(By.XPATH,'/html/body/main/header/div[2]/div/div[1]/div[2]/div[1]/ul/li/a').click()
    custom_sleep()
    #goto chainsaws
    driver.find_element(By.XPATH,'/html/body/main/section/div/div[1]/div[1]/ul/li[2]/ul/li[4]/a').click()
    custom_sleep()
    #item 3
    add_item_to_cart('/html/body/main/section/div/div[2]/section/section/div[3]/div/div[1]'
                     '/div[2]/article/div/a/img', 1)
    #item 4
    add_item_to_cart('/html/body/main/section/div/div[2]/section/section/div[3]/div/div[1]/div[1]'
                     '/article/div/a/img', 4)

def go_to_cart():
    custom_sleep()
    driver.find_element(By.XPATH, '/html/body/main/header/nav/div/div/div[1]/div[2]/div[2]/div/div/a/span[1]').click()
    custom_sleep()

def remove_item_from_cart():
    go_to_cart()
    custom_sleep()
    custom_sleep()
    driver.find_element(By.XPATH,'/html/body/main/section/div/div/section/div/div[1]/div/div[2]/ul/li'
                                 '/div/div[3]/div/div[2]/div/div[1]/div/span[3]/button[2]').click()
    custom_sleep()
    custom_sleep()

def make_order():
    #make order
    driver.find_element(By.XPATH,'/html/body/main/section/div/div/section/div/div[2]/div/div[2]/div').click()
    custom_sleep()
    #addres
    driver.find_element(By.XPATH,'/html/body/section/div/section/div/div[1]/section[2]/div/div/form/div/div/'
                                 'section/div[6]/div[1]/input').send_keys(ADDRES)
    custom_sleep()
    #post code
    driver.find_element(By.XPATH,'/html/body/section/div/section/div/div[1]/section[2]'
                                 '/div/div/form/div/div/section/div[8]/div[1]/input').send_keys(POST_CODE)
    custom_sleep()
    #city
    driver.find_element(By.XPATH, '/html/body/section/div/section/div/div[1]/'
                                  'section[2]/div/div/form/div/div/section/div[9]/div[1]/input').send_keys(CITY)
    custom_sleep()
    #forward
    driver.find_element(By.XPATH, '//*[@id="delivery-address"]/div/footer/button').click()
    custom_sleep()

    # delivery method
    driver.find_element(By.XPATH,'//*[@id="delivery_option_16"]').click()
    custom_sleep()
    #forward
    driver.find_element(By.XPATH, '/html/body/section/div/section/div/div[1]'
                                  '/section[3]/div/div[2]/form/button').click()
    custom_sleep()
    #payment method
    driver.find_element(By.XPATH, '//*[@id="payment-option-2"]').click()
    custom_sleep()
    #agreement
    driver.find_element(By.XPATH, '//*[@id="conditions_to_approve[terms-and-conditions]"]').click()
    custom_sleep()
    #commit order
    driver.find_element(By.XPATH, '//*[@id="payment-confirmation"]/div[1]/button').click()
    custom_sleep()

def check_status_of_order():
    #account
    driver.find_element(By.XPATH, '/html/body/main/header/nav/div/div/div[1]/div[2]/div[1]/div/a[2]/span').click()
    custom_sleep()
    #orders history
    driver.find_element(By.XPATH, '/html/body/main/section/div/div/section'
                                  '/section/div/div/a[3]/span').click()
    custom_sleep()
    #order details
    driver.find_element(By.XPATH, '/html/body/main/section/div/div/section'
                                  '/section/table/tbody/tr/td[6]/a[1]').click()
    custom_sleep()

driver.get('https://localhost:8080?id_lang=2')
fill_cart()
remove_item_from_cart()
register()
go_to_cart()
make_order()
check_status_of_order()
driver.close()
time.sleep(300)
time.sleep(200)


