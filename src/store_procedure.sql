DELIMITER //
CREATE PROCEDURE sp_login(
  IN _username VARCHAR(100),
  IN _password VARCHAR(255)
)
BEGIN
	SELECT id_user, firstname, lastname, username, area, cargo, status, chat_plus, at_created, at_updated, id_rol
    FROM users WHERE username = _username AND password = _password;
END //
DELIMITER ;

-- DROP PROCEDURE sp_login;
-- CALL sp_login('juan@pprios.com', 'juan');