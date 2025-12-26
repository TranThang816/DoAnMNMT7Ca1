<?php
require_once __DIR__ . '/../config/db.php';

class TuVanModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * Save consultation request with automatic doctor assignment or specified doctor
     */
    public function saveTuVan($hoten, $email, $sdt, $mota, $anhPath = null, $idbacsi = null) {
        try {
            $idkhoa = null;

            // If doctor is specified, get their specialty
            if ($idbacsi) {
                $bacsis = $this->getAllBacSi();
                foreach ($bacsis as $bs) {
                    if ($bs['ID'] === $idbacsi) {
                        $idkhoa = $bs['IDKHOA'] ?? null;
                        break;
                    }
                }
            } else {
                // Auto-assign: Find appropriate specialty based on symptoms
                $idkhoa = $this->findKhoaBySymptoms($mota);

                // If specialty found, assign a doctor
                if ($idkhoa) {
                    $idbacsi = $this->findBacSiByKhoa($idkhoa);
                }
            }

            // Insert consultation request
            if ($idbacsi && $idkhoa) {
                $sql = "INSERT INTO tuvan (HOTEN, EMAIL, SDT, MOTA, IDKHOA, IDBACSI, ANH, TRANGTHAI) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'Chờ xử lý')";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([$hoten, $email, $sdt, $mota, $idkhoa, $idbacsi, $anhPath]);
            } else if ($idkhoa) {
                $sql = "INSERT INTO tuvan (HOTEN, EMAIL, SDT, MOTA, IDKHOA, ANH, TRANGTHAI) 
                        VALUES (?, ?, ?, ?, ?, ?, 'Chờ xử lý')";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([$hoten, $email, $sdt, $mota, $idkhoa, $anhPath]);
            } else {
                $sql = "INSERT INTO tuvan (HOTEN, EMAIL, SDT, MOTA, ANH, TRANGTHAI) 
                        VALUES (?, ?, ?, ?, ?, 'Chờ xử lý')";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([$hoten, $email, $sdt, $mota, $anhPath]);
            }
        } catch(PDOException $e) {
            error_log("Error saving consultation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find specialty based on symptom keywords
     */
    private function findKhoaBySymptoms($mota) {
        $keywords = [
            'TIM MACH' => ['tim', 'tim mạch', 'đau ngực', 'khó thở', 'huyết áp', 'nhịp tim'],
            'NỘI TIẾT' => ['nội tiết', 'đường huyết', 'tiểu đường', 'gút', 'tuyến giáp'],
            'NHI KHOA' => ['nhi', 'trẻ em', 'trẻ nhỏ', 'em bé', 'bé'],
            'TAI MŨI HỌNG' => ['tai', 'mũi', 'họng', 'viêm họng', 'sổ mũi', 'nghẹt mũi', 'ho'],
            'SẢN PHỤ KHOA' => ['phụ khoa', 'sản', 'bà bầu', 'thai', 'kinh nguyệt'],
            'DA LIỄU' => ['da', 'mụn', 'ngứa', 'nổi mẩn', 'dị ứng da'],
            'UNG BƯỚU' => ['ung thư', 'ung bướu', 'u', 'khối u'],
            'NỘI TỔNG QUÁT' => ['đau bụng', 'tiêu hóa', 'đau đầu', 'sốt', 'mệt mỏi']
        ];

        $mota_lower = mb_strtolower($mota, 'UTF-8');
        
        foreach ($keywords as $tenkhoa => $keywords_list) {
            foreach ($keywords_list as $keyword) {
                if (stripos($mota_lower, $keyword) !== false) {
                    // Get IDKHOA from database
                    $sql = "SELECT IDKHOA FROM khoa WHERE TENKHOA = ?";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$tenkhoa]);
                    $result = $stmt->fetch();
                    if ($result) {
                        return $result['IDKHOA'];
                    }
                }
            }
        }
        
        return null;
    }

    /**
     * Find available doctor by specialty
     */
    private function findBacSiByKhoa($idkhoa) {
        try {
            $sql = "SELECT ID FROM bacsi WHERE IDKHOA = ? ORDER BY RAND() LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idkhoa]);
            $result = $stmt->fetch();
            return $result ? $result['ID'] : null;
        } catch(PDOException $e) {
            error_log("Error finding doctor: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all consultation requests
     */
    public function getAllTuVan() {
        try {
            $sql = "SELECT t.*, k.TENKHOA, b.HOTEN as TenBacSi 
                    FROM tuvan t
                    LEFT JOIN khoa k ON t.IDKHOA = k.IDKHOA
                    LEFT JOIN bacsi b ON t.IDBACSI = b.ID
                    ORDER BY t.NgayTao DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error getting consultations: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get consultation by ID
     */
    public function getTuVanById($id) {
        try {
            $sql = "SELECT t.*, k.TENKHOA, b.HOTEN as TenBacSi, b.EMAIL as EmailBacSi 
                    FROM tuvan t
                    LEFT JOIN khoa k ON t.IDKHOA = k.IDKHOA
                    LEFT JOIN bacsi b ON t.IDBACSI = b.ID
                    WHERE t.ID = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Error getting consultation: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update consultation status and response
     */
    public function updateTuVan($id, $trangthai, $phanhoi = null) {
        try {
            $sql = "UPDATE tuvan SET TRANGTHAI = ?, PHANHOI = ?, NgayCapNhat = NOW() WHERE ID = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$trangthai, $phanhoi, $id]);
        } catch(PDOException $e) {
            error_log("Error updating consultation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete consultation by ID
     */
    public function deleteTuVan($id) {
        try {
            $sql = "DELETE FROM tuvan WHERE ID = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            error_log("Error deleting consultation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all specialties
     */
    public function getAllKhoa() {
        try {
            $sql = "SELECT * FROM khoa ORDER BY TENKHOA";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error getting specialties: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all doctors
     */
    public function getAllBacSi() {
        try {
            $sql = "SELECT b.*, k.TENKHOA 
                    FROM bacsi b
                    LEFT JOIN khoa k ON b.IDKHOA = k.IDKHOA
                    ORDER BY b.HOTEN";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error getting doctors: " . $e->getMessage());
            return [];
        }
    }
}
?>